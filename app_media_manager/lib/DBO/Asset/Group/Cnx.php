<?php
class DBO_Asset_Group_Cnx {
	const TABLE_NAME = "asset_group_cnx";
	const MODEL_NAME = "DBO_Asset_Group_Cnx_Model";
	const SELECT_COLUMNS = "a.id,a.asset_id,a.group_id";
	
	public static function getOneById($cnx_id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::SELECT_COLUMNS . "
			FROM asset_group_cnx AS a
			WHERE a.id = " . Ode_DBO::getInstance()->quote($cnx_id, PDO::PARAM_INT) . "
		")->fetchObject(self::MODEL_NAME);
	}
	
	public static function removeAllAssets($group_id) {
		$sth = Ode_DBO::getInstance()->prepare("DELETE FROM " . self::TABLE_NAME . " WHERE group_id = :id");
		$sth->bindParam(":id", $group_id, PDO::PARAM_STR, 50);
		
		try {
			
		} catch(PDOException $e) {
			error_log($e->getTraceAsString(), 0);
		} catch(Exception $e) {
			error_log($e->getTraceAsString(), 0);
		}
		
		return true;
	}
	
	public static function assignAssetToGroup($asset_id, $group_id) {
		Ode_DBO::getInstance()->beginTransaction();
		
		$sth = Ode_DBO::getInstance()->prepare("
			INSERT INTO " . self::TABLE_NAME . " (asset_id, group_id)
			VALUES (:asset_id, :group_id)
		");
		$sth->bindValue(":asset_id", $asset_id, PDO::PARAM_STR);
		$sth->bindValue(":group_id", $group_id, PDO::PARAM_STR);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
            error_log($e->getMessage(), 0);
		}
		
		$sth = Ode_DBO::getInstance()->prepare("
			INSERT INTO asset_group_def (cnx_id)
			VALUES (LAST_INSERT_ID())
		");
		
		try {
			Ode_DBO::getInstance()->exec("INSERT INTO asset_group_def (cnx_id) VALUES (LAST_INSERT_ID())");
		} catch(PDOException $e) {
			//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
            error_log($e->getMessage(), 0);
		}
		
		Ode_DBO::getInstance()->commit();
	}
	
	public static function removeAssetFromGroup($asset_id, $group_id) {
		$cnx = self::getOneByAssetAndGroup($asset_id, $group_id);
		
		if($cnx != false) {
			try {
				DBO_Asset_Group_Def::removeByCnx($cnx->id);
				
				try {
					self::remove($cnx->id);
				} catch(Exception $e) {
					//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                    error_log($e->getMessage(), 0);
				}
			} catch(Exception $e) {
				//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                error_log($e->getMessage(), 0);
			}
		}
	}
	
	public static function remove($cnx_id) {
		$sth = Ode_DBO::getInstance()->prepare("DELETE FROM " . self::TABLE_NAME . " WHERE id = :id");
		$sth->bindValue(":id", $cnx_id, PDO::PARAM_INT);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
            error_log($e->getMessage(), 0);
		}
	}
	
	public static function getOneByAssetAndGroup($asset_id, $group_id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::SELECT_COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.asset_id = " . Ode_DBO::getInstance()->quote($asset_id, PDO::PARAM_STR) . "
			AND a.group_id = " . Ode_DBO::getInstance()->quote($group_id, PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchObject(self::MODEL_NAME);
	}
	
	public static function setDefaultAsset($asset_id, $group_id) {
		$defIds = Ode_DBO::getInstance()->query("
			SELECT def.id
			FROM asset_group_def AS def
			LEFT JOIN " . self::TABLE_NAME . " AS cnx ON (cnx.id = def.cnx_id)
			WHERE cnx.group_id = " . Ode_DBO::getInstance()->quote($group_id, PDO::PARAM_STR) . "
		")->fetchAll(PDO::FETCH_OBJ);
		
		$newDefIds = array();
		foreach($defIds as $obj) {
			$newDefIds[] = $obj->id;
		}
		
		$defId = Ode_DBO::getInstance()->query("
			SELECT def.id
			FROM asset_group_def AS def
			LEFT JOIN " . self::TABLE_NAME . " AS cnx ON (cnx.id = def.cnx_id)
			WHERE cnx.asset_id = " . Ode_DBO::getInstance()->quote($asset_id, PDO::PARAM_STR) . "
			AND cnx.group_id = " . Ode_DBO::getInstance()->quote($group_id, PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchColumn();
		
		Ode_DBO::getInstance()->beginTransaction();
		
		Ode_DBO::getInstance()->exec("UPDATE asset_group_def SET is_default = 0 WHERE id IN (" . implode(",", Util::dbQuoteListItems($newDefIds)) . ")");
		
		$sth = Ode_DBO::getInstance()->prepare("UPDATE asset_group_def SET is_default = 1 WHERE id = :id");
		$sth->bindValue(":id", $defId, PDO::PARAM_INT);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
            error_log($e->getMessage(), 0);
		}
		
		Ode_DBO::getInstance()->commit();
		
		return array("def_ids" => $defIds, "asset_def_id" => $defId);
	}
}