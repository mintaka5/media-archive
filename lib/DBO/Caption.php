<?php
class DBO_Caption {
	const TABLE_NAME = "captions";
	const MODEL_NAME = "DBO_Caption_Model";
	const TYPE_TABLE_NAME = "caption_types";
	const COLUMNS = "a.id, a.type_id, a.asset_id, a.caption, a.modified_by, a.is_active, a.created, a.modified";
	
	public static function getOneByTypeAndAsset($asset_id, $type_name) {
		$sql = "
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			LEFT JOIN " . self::TYPE_TABLE_NAME . " AS b ON (b.id = a.type_id) 
			WHERE a.asset_id = " . Ode_DBO::getInstance()->quote($asset_id, PDO::PARAM_STR) . "
			AND b.capn_name = " . Ode_DBO::getInstance()->quote($type_name, PDO::PARAM_STR) . "
			AND a.is_active = 1
			LIMIT 0,1
		";
		
		return Ode_DBO::getInstance()->query($sql)->fetchObject(self::MODEL_NAME);
	}
	
	public static function assign($asset_id, $caption, $type_id, $user_id) {
		$cap = self::getOneByTypeIdAndAsset($asset_id, $type_id);
		
		$capId = false;
		if($cap != false) {
			$sth = Ode_DBO::getInstance()->prepare("
				UPDATE " . self::TABLE_NAME . "
				SET
					caption = :caption,
					modified_by = :user,
					modified = NOW()
				WHERE id = :id
			");
			$sth->bindValue(":caption", trim($caption), PDO::PARAM_STR);
			$sth->bindValue(":user", $user_id, PDO::PARAM_STR);
			$sth->bindValue(":id", $cap->id, PDO::PARAM_INT);
			
			try {
				$sth->execute();
			} catch(PDOException $e) {
				//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                error_log($e->getMessage(), 0);
			}
			
			$capId = $cap->id;
		} else {
			//Ode_DBO::getInstance()->beginTransaction();
			
			$sth = Ode_DBO::getInstance()->prepare("
				INSERT INTO " . self::TABLE_NAME . " (type_id, asset_id, caption, modified_by, is_active, created, modified)
				VALUES (:type, :asset, :caption, :user, 1, NOW(), NOW())
			");
			$sth->bindValue(":type", $type_id, PDO::PARAM_INT);
			$sth->bindValue(":asset", $asset_id, PDO::PARAM_STR);
			$sth->bindValue(":caption", trim($caption), PDO::PARAM_STR);
			$sth->bindValue(":user", $user_id, PDO::PARAM_STR);
			
			try {
				$sth->execute();
			} catch(PDOException $e) {
				//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                error_log($e->getMessage(), 0);
			}
			
			$capId = Ode_DBO::getInstance()->query("SELECT LAST_INSERT_ID()")->fetchColumn();
			
			//Ode_DBO::getInstance()->commit();
		}
		
		return $capId;
	}
	
	public static function getOneByTypeIdAndAsset($asset_id, $type_id) {
		$caption = Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . " 
			FROM " . self::TABLE_NAME . " AS a 
			WHERE a.asset_id = " . Ode_DBO::getInstance()->quote($asset_id, PDO::PARAM_STR) . "
			AND a.type_id = " . Ode_DBO::getInstance()->quote($type_id, PDO::PARAM_INT) . "
			LIMIT 0,1
		")->fetchObject(self::MODEL_NAME);
		
		return $caption;
	}
}