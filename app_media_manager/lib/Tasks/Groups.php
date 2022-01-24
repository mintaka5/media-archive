<?php
class Tasks_Groups {
    public function __construct() {}
    
    public static function add($title, $user_id, $user_org_ids) {
        $uuid = UUID::get(); // establish new unique ID
        
        // insert new record into database
        $sth = Ode_DBO::getInstance()->prepare("
                INSERT INTO " . DBO_Group::TABLE_NAME . " (id, title, is_approved, is_deleted, created, modified, modified_by)
                VALUES (:id, :title, 0, 0, NOW(), NOW(), :user)");
        $sth->bindParam(":id", $uuid, PDO::PARAM_STR, 50);
        $sth->bindParam(":title", trim($title), PDO::PARAM_STR, 45);
        $sth->bindParam(":user", $user_id, PDO::PARAM_STR, 50);
        
        try {
            $sth->execute();
            
            self::assignToOrganizations($uuid, $user_org_ids);
        } catch(PDOException $e) {
            error_log($e->getTraceAsString(), 0);
            
            return false;
        } catch(Exception $e) {
            error_log($e->getTraceAsString(), 0);
            
            return false;
        }
        
        return $uuid;
    }
    
    public static function assignToOrganizations($group_id, $org_ids) {
        if(empty($org_ids)) { // if user's orgs are not selected use them all
            DBO_Group::assignOrganizationsByUser($group_id, Ode_Auth::getInstance()->getSession()->id);
        } else { // just assign the ones selected
            foreach ($org_ids as $org_id) {
                DBO_Group_Metadata::add(DBO_Group_Metadata::META_ORG_ID_NAME, $org_id, $group_id);
            }
        }
    }
    
    /**
     * Grab all available groups for an asset. Assets cannot be reassigned to a group to which they already are assigned.
     * Assets are only selectively avaialable based on user organization and role
     * @param Ode_Auth $auth
     * @param DBO_Asset_Model $asset
     * @param type $fetch_type
     * @return mixed array|DBO_Group_Model[]
     */
    public static function availableToAsset(Ode_Auth $auth, DBO_Asset_Model $asset, $fetch_type = PDO::FETCH_ASSOC) {
        if($auth->isAdmin()) {
            $sql = "SELECT " . DBO_Group::COLUMNS . "
                    FROM " . DBO_Group::TABLE_NAME . " AS a
                    WHERE a.is_deleted = 0
                    AND a.id NOT IN (
                            SELECT group_id 
                            FROM asset_group_cnx 
                            WHERE asset_id = " . Ode_DBO::getInstance()->quote($asset->id, PDO::PARAM_STR) . "
                    )
                    ORDER BY a.title
                    ASC";
        } else {
            $org_ids = Util::dbQuoteListItems(DBO_User_Organization_Cnx::getUserOrgIDs($auth->getSession()->id));
            $org_ids = (!empty($org_ids)) ? implode(',', $org_ids) : "''";
            
            $sql = "SELECT " . DBO_Group::COLUMNS . "
                    FROM " . DBO_Group::TABLE_NAME . " AS a
                    LEFT JOIN " . DBO_Group_Metadata::TABLE_NAME . " AS b ON (b.group_id = a.id)
                    WHERE a.is_deleted = 0
                    AND a.id NOT IN (
                            SELECT group_id 
                            FROM asset_group_cnx 
                            WHERE asset_id = " . Ode_DBO::getInstance()->quote($asset->id, PDO::PARAM_STR) . "
                    )
                    AND b.meta_name = " . Ode_DBO::getInstance()->quote(DBO_Group_Metadata::META_ORG_ID_NAME, PDO::PARAM_STR) . "
                    AND b.meta_value IN (" . $org_ids . ")
                    ORDER BY a.title
                    ASC";
        }
        
        $query = Ode_DBO::getInstance()->query($sql);
        
        if($fetch_type == PDO::FETCH_ASSOC) {
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return $query->fetchAll(PDO::FETCH_CLASS, DBO_Group::MODEL_NAME);
        }
    }
    
    public static function searchAvailable(Ode_Auth $auth, $query, DBO_Asset_Model $asset, $fetch_type = PDO::FETCH_ASSOC) {
        $query = preg_replace("[\W\s\t\n\r]", "%", trim($query));
        
        if($auth->isAdmin()) {
            $sql = "SELECT " . DBO_Group::COLUMNS . "
                    FROM " . DBO_Group::TABLE_NAME . " AS a
                    WHERE a.is_deleted = 0
                    AND a.id NOT IN (
                        SELECT group_id
                        FROM asset_group_cnx
                        WHERE asset_id = " . Ode_DBO::getInstance()->quote($asset->id, PDO::PARAM_STR) . "
                    )
                    AND a.title LIKE '%" . $query . "%'
                    ORDER BY a.title
                    ASC";
        } else {
            $org_ids = Util::dbQuoteListItems(DBO_User_Organization_Cnx::getUserOrgIDs($auth->getSession()->id));
            $org_ids = (!empty($org_ids)) ? implode(',', $org_ids) : "''";
            
            $sql = "SELECT " . DBO_Group::COLUMNS . "
                    FROM " . DBO_Group::TABLE_NAME . " AS a
                    LEFT JOIN " . DBO_Group_Metadata::TABLE_NAME . " AS b ON (b.group_id = a.id)
                    WHERE a.is_deleted = 0
                    AND a.id NOT IN (
                            SELECT group_id 
                            FROM asset_group_cnx 
                            WHERE asset_id = " . Ode_DBO::getInstance()->quote($asset->id, PDO::PARAM_STR) . "
                    )
                    AND b.meta_name = " . Ode_DBO::getInstance()->quote(DBO_Group_Metadata::META_ORG_ID_NAME, PDO::PARAM_STR) . "
                    AND b.meta_value IN (" . $org_ids . ")
                    AND a.title LIKE '%" . $query . "%'
                    ORDER BY a.title
                    ASC";
        }
        
        $query = Ode_DBO::getInstance()->query($sql);
        
        if($fetch_type == PDO::FETCH_ASSOC) {
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return $query->fetchAll(PDO::FETCH_CLASS, DBO_Group::MODEL_NAME);
        }
    }
}
?>
