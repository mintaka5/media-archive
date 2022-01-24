<?php
class DBO_Asset_Outtake {
	const TABLE_NAME = "asset_outtakes";
	const MODEL_NAME = "DBO_Asset_Outtake_Model";
	const COLUMNS = "a.id, a.asset_id";
	
	public static function getOneByAsset($asset_id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.asset_id = " . Ode_DBO::getInstance()->quote($asset_id, PDO::PARAM_STR) . "
			LIMIT 0,1 
		")->fetchObject(self::MODEL_NAME);
	}
	
	public static function set($asset_id) {
		$isOuttake = self::getOneByAsset($asset_id);
		
		if($isOuttake == false) {
			$sth = Ode_DBO::getInstance()->prepare("
				INSERT INTO " . self::TABLE_NAME . " (asset_id)
				VALUES (:asset)
			");
			$sth->bindValue(":asset", $asset_id, PDO::PARAM_STR);
			
			try {
				$sth->execute();
			} catch(PDOException $e) {
				//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                error_log($e->getMessage(), 0);
			}
		}
		
		return;
	}
	
	public static function un_set($asset_id) {
		$isOuttake = self::getOneByAsset($asset_id);
		
		if($isOuttake != false) {
			$sth = Ode_DBO::getInstance()->prepare("DELETE FROM " . self::TABLE_NAME . " WHERE asset_id = :asset");
			$sth->bindValue(":asset", $asset_id, PDO::PARAM_STR);
			
			try {
				$sth->execute();
			} catch(PDOException $e) {
				//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                error_log($e->getMessage(), 0);
			}
		}
		
		return;
	}
}