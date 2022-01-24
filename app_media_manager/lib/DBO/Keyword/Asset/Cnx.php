<?php
class DBO_Keyword_Asset_Cnx {
	const TABLE_NAME = "keyword_asset_cnx";
	const MODEL_NAME = "DBO_Keyword_Asset_Cnx_Model";
	const COLUMNS = "a.id, a.keyword_id, a.asset_id";
	
	public static function unassign($asset_id, $keyword) {
		$cnxId = Ode_DBO::getInstance()->query("
			SELECT a.id
			FROM " . DBO_Keyword_Asset_Cnx::TABLE_NAME . " AS a
			LEFT JOIN " . DBO_Keyword::TABLE_NAME . " AS b ON (b.id = a.keyword_id)
			WHERE b.keyword = " . Ode_DBO::getInstance()->quote($keyword, PDO::PARAM_STR) . "
			AND a.asset_id = " . Ode_DBO::getInstance()->quote($asset_id, PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchColumn();
		
		if($cnxId != false) {
			$sth = Ode_DBO::getInstance()->prepare("DELETE FROM " . DBO_Keyword_Asset_Cnx::TABLE_NAME . " WHERE id = :id");
			$sth->bindValue(":id", $cnxId, PDO::PARAM_INT);
			
			try {
				$sth->execute();
			} catch(PDOException $e) {
				//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                error_log($e->getMessage(), 0);
			}
		}
		
		return;
	}
	
	public static function assignById($asset_id, $keyword_id) {
		if(!self::exists($asset_id, $keyword_id)) {
			$sth = Ode_DBO::getInstance()->prepare("INSERT INTO " . self::TABLE_NAME . " (keyword_id, asset_id) VALUES (:keyword, :asset)");
			$sth->bindValue(":keyword", $keyword_id, PDO::PARAM_INT);
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
	
	public static function exists($asset_id, $keyword_id) {
		$cnx = self::getOneByAssetAndKeyword($asset_id, $keyword_id);
		
		if($cnx != false) {
			return true;
		}
		
		return false;
	}
	
	public static function assign($asset_id, $keyword) {
		$kword = DBO_Keyword::getOneByKeyword($keyword);
		
		Ode_DBO::getInstance()->beginTransaction();
		
		$kwordId = $kword->id;
		if($kword == false) {
			// insert new keyword
		}
		
		$cnx = self::getOneByAssetAndKeyword($asset_id, $kwordId);
		if($cnx != false) {
			// update
			$sth = Ode_DBO::getInstance()->prepare("
				UPDATE " . DBO_Keyword_Asset_Cnx::TABLE_NAME . "
				SET 
					keyword_id = :kword
				WHERE id = :id
			");
			$sth->bindValue(":kword", $kwordId, PDO::PARAM_INT);
			$sth->bindValue(":id", $cnx->id, PDO::PARAM_INT);
			
			try {
				$sth->execute();
			} catch(PDOException $e) {
				//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                error_log($e->getMessage(), 0);
			}
		} else {
			$sth = Ode_DBO::getInstance()->prepare("
				INSERT INTO " . DBO_Keyword_Asset_Cnx::TABLE_NAME . " (keyword_id, asset_id)
				VALUES (:kword, :asset)
			");
			$sth->bindValue(":kword", $kwordId, PDO::PARAM_INT);
			$sth->bindValue(":asset", $asset_id, PDO::PARAM_STR);
			
			try {
				$sth->execute();
			} catch(PDOException $e) {
				//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                error_log($e->getMessage(), 0);
			}
		}
		
		Ode_DBO::getInstance()->commit();
		
		return;
	} 
	
	public function getOneByAssetAndKeyword($asset_id, $keyword_id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.asset_id = " . Ode_DBO::getInstance()->quote($asset_id, PDO::PARAM_STR) . "
			AND a.keyword_id = " . Ode_DBO::getInstance()->quote($keyword_id, PDO::PARAM_INT) . "
			LIMIT 0,1
		")->fetchObject(self::MODEL_NAME);
	}
}