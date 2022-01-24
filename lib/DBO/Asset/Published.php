<?php
class DBO_Asset_Published {
	const TABLE_NAME = "asset_published";
	const MODEL_NAME = "DBO_Asset_Published_Model";
	const COLUMNS = "a.id, a.asset_id, a.pub_id, a.pub_date, a.user_id, a.created";
	
	public static function getOneByAsset($asset_id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.asset_id = " . Ode_DBO::getInstance()->quote($asset_id, PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchObject(self::MODEL_NAME);
	}
	
	public static function set($asset_id, $title, $date) {
		$title = trim($title);
		$date = date("Y-m-d H:i:s", strtotime(trim($date)));
		
		//Ode_DBO::getInstance()->beginTransaction();
	
		$pub = DBO_Publication::getOneByTitle($title);
	
		if($pub == false) {
			$pubId = DBO_Publication::add($title);
		} else {
			$pubId = $pub->id;	
		}
		
		$pubd = self::getOneByAssetAndPublicationAndDate($asset_id, $pubId, $date);
		
		if($pubd == false) {
			$sth = Ode_DBO::getInstance()->prepare("
				INSERT INTO " . self::TABLE_NAME . " (asset_id, pub_id, pub_date, user_id, created)
				VALUES (:asset, :pub, :date, :user, NOW())
			");
			$sth->bindValue(":asset", $asset_id, PDO::PARAM_STR);
			$sth->bindValue(":pub", $pubId, PDO::PARAM_INT);
			$sth->bindValue(":date", $date, PDO::PARAM_STR);
			$sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
			
			try {
				$sth->execute();
			} catch(PDOException $e) {
				//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                error_log($e->getMessage(), 0);
				
				return false;
			}
		} else {
			$sth = Ode_DBO::getInstance()->prepare("
				UPDATE " . self::TABLE_NAME . "
				SET
					pub_id = :pub,
					pub_date = :date,
					user_id = :user
					created = NOW()
				WHERE id = :id
			");
			$sth->bindValue(":pub", $pubId, PDO::PARAM_INT);
			$sth->bindValue(":date", $date, PDO::PARAM_STR);
			$sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
			$sth->bindValue(":id", $pubd->id, PDO::PARAM_INT);
			
			try {
				$sth->execute();
			} catch(PDOException $e) {
				//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                error_log($e->getMessage(), 0);
			
				return false;
			}
		}
		
		//Ode_DBO::getInstance()->commit();
		
		return true;
	}
	
	public static function getOneByAssetAndPublicationAndDate($asset_id, $pub_id, $date) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.asset_id = " . Ode_DBO::getInstance()->quote($asset_id, PDO::PARAM_STR) . "
			AND a.pub_id = " . Ode_DBO::getInstance()->quote($pub_id, PDO::PARAM_INT) . "
			AND a.pub_date = " . Ode_DBO::getInstance()->quote($date, PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchObject(self::MODEL_NAME);
	}
	
	public static function un_set($asset_id) {
		$sth = Ode_DBO::getInstance()->prepare("DELETE FROM " . self::TABLE_NAME . " WHERE asset_id = :asset");
		$sth->bindValue(":asset", $asset_id, PDO::PARAM_STR);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
            error_log($e->getMessage(), 0);
		
			return false;
		}
		
		return true;
	}
	
	public static function deleteByAsset($asset_id) {
		$sth = Ode_DBO::getInstance()->prepare("DELETE FROM " . self::TABLE_NAME . " WHERE asset_id = :asset");
		$sth->bindValue(":asset", $asset_id, PDO::PARAM_STR);
		
		try {
			$sth->execute();
		} catch (PDOException $e) {
			//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
            error_log($e->getMessage(), 0);
		}
		
		return;
	}
}