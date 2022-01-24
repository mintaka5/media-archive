<?php
class DBO_Group {
	const TABLE_NAME = "groups";
	const MODEL_NAME = "DBO_Group_Model";
	const COLUMNS = "a.id,a.title,a.date_start,a.date_end,a.is_approved,a.is_deleted,a.created,a.modified,a.modified_by";

	public static function findByUserOrgs($query_string, $user_id) {
		$user = DBO_User::getOneById($user_id);
		
		$query = "%" . preg_replace("/[\s\r\n\t\W]+/", "%", $query_string) . "%";
		
		if($user->type()->type_name == DBO_User_Model::ADMIN_TYPE) {
			$sql = "SELECT " . self::COLUMNS . "
					FROM " . self::TABLE_NAME . " AS a
					WHERE a.title LIKE " . Ode_DBO::getInstance()->quote($query, PDO::PARAM_STR) . "
					AND a.is_deleted = 0
					ORDER BY a.title
					ASC";
		} else {
			$org_ids = Util::dbQuoteListItems(DBO_User_Organization_Cnx::getUserOrgIDs($user->id));
			$org_ids = (!empty($org_ids)) ? implode(",", $org_ids) : "''";
			
			$sql = "SELECT " . self::COLUMNS . "
					FROM " . self::TABLE_NAME . " AS a
					LEFT JOIN " . DBO_Group_Metadata::TABLE_NAME . " AS b ON (b.group_id = a.id)
					WHERE a.is_deleted = 0
					AND a.title LIKE " . Ode_DBO::getInstance()->quote($query, PDO::PARAM_STR) . "
					AND b.meta_name = " . Ode_DBO::getInstance()->quote(DBO_Group_Metadata::META_ORG_ID_NAME, PDO::PARAM_STR) . "
					AND b.meta_value IN (" . $org_ids . ")
					GROUP BY a.id";
		}
		
		return Ode_DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_CLASS, DBO_Group::MODEL_NAME);
	}
	
	public static function getAllByContainer($id) {
		return Ode_DBO::getInstance()->query("
			SELECT `group`.*
			FROM group_container_cnx AS cnx
			LEFT JOIN groups AS `group` ON (`group`.id = cnx.group_id)
			WHERE cnx.container_id = " . Ode_DBO::getInstance()->quote($id, PDO::PARAM_STR) . "
			AND `group`.is_deleted = 0
		")->fetchAll(PDO::FETCH_CLASS, "DBO_Group_Model");
	}
	
	public static function getAllPublicByContainer($container_id, $limit = false) {
		$sql = "
			SELECT " . self::COLUMNS . "
			FROM " . DBO_Container_Metadata::TABLE_NAME . " AS b
			LEFT JOIN " . self::TABLE_NAME . " AS a ON (a.id = b.meta_value)
			WHERE b.container_id = " . Ode_DBO::getInstance()->quote($container_id, PDO::PARAM_STR) . "
			AND b.meta_key = " . Ode_DBO::getInstance()->quote(DBO_Container_Metadata::META_GROUP_ID_NAME, PDO::PARAM_STR) . "
			AND a.is_approved = 1
			AND a.is_deleted = 0
		";
		
		if($limit != false) {
			$sql .= " LIMIT 0," . $limit;
		}
		
		return Ode_DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_CLASS, self::MODEL_NAME);
	}
	
	public static function getAllByAsset($asset_id) {
		return Ode_DBO::getInstance()->query("
			SELECT `group`.*
			FROM asset_group_cnx AS cnx
			LEFT JOIN groups AS `group` ON (`group`.id = cnx.group_id)
			WHERE cnx.asset_id = " . Ode_DBO::getInstance()->quote($asset_id, PDO::PARAM_STR) . "
			AND group.is_deleted = 0
			ORDER BY `group`.title
		")->fetchAll(PDO::FETCH_CLASS, "DBO_Group_Model");
	}
	
	public static function getOneById($group_id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.id = " . Ode_DBO::getInstance()->quote($group_id, PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchObject("DBO_Group_Model");
	}
	
	public static function addUpload($filename, $mime_type, $user_id, $group_id) {
		try {
			$assetId = DBO_Asset::addFullUpload($filename, $mime_type, $user_id);
			
			/**
			 * Assign the groups organizations to the asset being uploaded to the group
			 */
			$orgs = DBO_Group_Metadata::get(DBO_Group_Metadata::META_ORG_ID_NAME, $group_id, false);
			foreach($orgs as $org) {
				DBO_Asset_Metadata::add(DBO_Asset_Metadata::META_ORG_ID_NAME, $org->meta_value, $assetId);
			}
			
			try {
				DBO_Asset_Group_Cnx::assignAssetToGroup($assetId, $group_id);
			} catch (Exception $e) {
				//Ode_Log::getInstance()->log($e->getMessage(), E_USER_ERROR);
                error_log($e->getMessage(), 0);
			}
		} catch(Exception $e) {
			//Ode_Log::getInstance()->log($e->getMessage(), E_USER_ERROR);
            error_log($e->getMessage(), 0);
		}
	}
	
	public static function getDefaultAsset($group_id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . DBO_Asset::COLUMNS . "
			FROM " . DBO_Asset_Group_Cnx::TABLE_NAME . " AS cnx
			LEFT JOIN asset_group_def AS def ON (def.cnx_id = cnx.id)
			LEFT JOIN " . DBO_Asset::TABLE_NAME . " AS a ON (a.id = cnx.asset_id)
			WHERE cnx.group_id = " . Ode_DBO::getInstance()->quote($group_id, PDO::PARAM_STR) . "
			AND a.is_deleted = 0
			AND a.is_active = 1
			AND def.is_default = 1
			LIMIT 0,1
		")->fetchObject("DBO_Asset_Model");
	}
	
	public static function addPhotographer($fname, $lname, $group_id, $user_id) {
		$assets = DBO_Asset::getAllByGroup($group_id);
		
		$photogId = DBO_Photographer::add($fname, $lname, $user_id);
		
		Ode_DBO::getInstance()->beginTransaction();
		
		foreach($assets as $asset) {
			$sth = Ode_DBO::getInstance()->prepare("
				UPDATE " . DBO_Asset::TABLE_NAME . "
				SET
					photographer_id = :photog
					modified_by = :user,
					modified = NOW()
				WHERE id = :id
			");
			$sth->bindValue(":photog", $photogId, PDO::PARAM_INT);
			$sth->bindValue(":user", $user_id, PDO::PARAM_STR);
			$sth->bindValue(":id", $asset->id, PDO::PARAM_STR);
			
			try {
				$sth->execute();
			} catch(PDOException $e) {
				//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                error_log($e->getMessage(), 0);
				
				return false;
			}
		}
		
		Ode_DBO::getInstance()->commit();
		
		return $photogId;
	}
	
	public static function approve($group_id, $approved = true) {
		$appr = ($approved == true) ? 1 : 0;
		
		$sth = Ode_DBO::getInstance()->prepare("
			UPDATE " . DBO_Group::TABLE_NAME . "
			SET
				is_approved = :appr,
				modified = NOW(),
				modified_by = :user
			WHERE id = :id
		");
		$sth->bindValue(":appr", $appr, PDO::PARAM_INT);
		$sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
		$sth->bindValue(":id", $group_id, PDO::PARAM_STR);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
            error_log($e->getMessage(), 0);
		
			return false;
		}
		
		return ($appr == 0) ? 1 : 0;
	}
	
	/**
	 * Assign a group to all user's assigned organizations
	 * @param string $group_id
	 * @param string $user_id
	 * @return boolean
	 */
	public static function assignOrganizationsByUser($group_id, $user_id) {
		$orgs = DBO_User_Organization_Cnx::getAllByUser($user_id);
		foreach($orgs as $org) {
			/**
			 * Only assign to group if not already assigned
			 */
			if(DBO_Group_Metadata::valueExists(DBO_Group_Metadata::META_ORG_ID_NAME, $org->org_id, $group_id) == false) {
				DBO_Group_Metadata::add(DBO_Group_Metadata::META_ORG_ID_NAME, $org->org_id, $group_id);
			}
		}
		
		return true;
	}
	
	public static function remove($group_id) {
		$sth = Ode_DBO::getInstance()->prepare("DELETE FROM " . self::TABLE_NAME . " WHERE id = :id");
		$sth->bindParam(":id", $group_id, PDO::PARAM_STR, 50);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			error_log($e->getTraceAsString(), 0);
		} catch(Exception $e) {
			error_log($e->getTraceAsString(), 0);
		}
		
		return true;
	}
}