<?php
class Tasks_Assets {
    public function __construct() {
        
    }
    
    public static function assignGroup($asset_id, $group_id) {
        Ode_DBO::getInstance()->beginTransaction();
        
        /*
         * Insert asset/group relationship
         */
        $sth = Ode_DBO::getInstance()->prepare("
            INSERT INTO asset_group_cnx (asset_id, group_id)
            VALUES (:asset_id, :group_id)
        ");
        $sth->bindValue(":asset_id", $asset_id, PDO::PARAM_STR);
        $sth->bindValue(":group_id", $group_id, PDO::PARAM_STR);
        
        try {
            $sth->execute();
            
            /*
             * Grab the asset/group relationship's ID
             */
            $cnxId = Ode_DBO::getInstance()->query("
                    SELECT a.id 
                    FROM " . DBO_Asset_Group_Cnx::TABLE_NAME . " AS a
                    WHERE a.asset_id = " . Ode_DBO::getInstance()->quote($asset_id, PDO::PARAM_STR) . "
                    AND a.group_id = " . Ode_DBO::getInstance()->quote($group_id, PDO::PARAM_STR) . "
                    LIMIT 0,1
            ")->fetchColumn();

            /*
             * Use the new ID to add relationship to the definitions table.
             */
            $sth = Ode_DBO::getInstance()->prepare("
                    INSERT IGNORE INTO asset_group_def (cnx_id)
                    VALUES (:cnx_id)
            ");
            $sth->bindValue(":cnx_id", $cnxId, PDO::PARAM_INT);

            try {
                    $sth->execute();
            } catch (PDOException $e) {
                    error_log($e->getTraceAsString(), 0);

                    Util::json(false);
            }
        } catch(PDOException $e) {
            error_log($e->getTraceAsString(), 0);
            
            Util::json(false);
        } catch(Exception $e) {
            error_log($e->getTraceAsString(), 0);
            
            Util::json(false);
        }
        
        Ode_DBO::getInstance()->commit();
    }
}
?>
