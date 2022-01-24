<?php
class DBO_Keyword {
	const TABLE_NAME = "keywords";
	const MODEL_NAME = "DBO_Keyword_Model";
	const COLUMNS = "a.id,a.keyword,a.is_deleted";
	
	public static function getOneByKeyword($keyword) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.keyword = " . Ode_DBO::getInstance()->quote(strtolower($keyword), PDO::PARAM_STR) . "
			AND a.is_deleted = 0
			LIMIT 0,1
		")->fetchObject(self::MODEL_NAME);
	}
	
	public static function getOneById($id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.id = " . Ode_DBO::getInstance()->quote($id, PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchObject(self::MODEL_NAME);
	}
	
	public static function getAllByAsset($id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . DBO_Keyword_Asset_Cnx::TABLE_NAME . " AS cnx
			LEFT JOIN " . self::TABLE_NAME . " AS a ON (a.id = cnx.keyword_id)
			WHERE cnx.asset_id = " . Ode_DBO::getInstance()->quote($id, PDO::PARAM_STR) . "
			AND a.is_deleted = 0
			ORDER BY a.keyword
			ASC
		")->fetchAll(PDO::FETCH_CLASS, self::MODEL_NAME);
	}
	
	public static function exists($keyword) {
		$term = self::getOneByKeyword($keyword);
		
		if($term != false) {
			return true;
		}
		
		return false;
	}
	
	public static function bulkAdd(array $keywords) {
		$ids = array();
		
		if(is_array($keywords)) {
			foreach($keywords as $keyword) {
				$ids[] = self::add($keyword);
			}
		}
		
		return $ids;
	}
	
	public static function add($keyword) {
		$keyword = trim($keyword);
		
		$kword = self::getOneByKeyword($keyword);
		
		if($kword == false) {
			$sth = Ode_DBO::getInstance()->prepare("
				INSERT INTO " . DBO_Keyword::TABLE_NAME . " (keyword, is_deleted)
				VALUES (:keyword, 0)
			");
			$sth->bindValue(":keyword", $keyword, PDO::PARAM_STR);
				
			try {
				$sth->execute();
				
				$kwordId = Ode_DBO::getInstance()->query("SELECT LAST_INSERT_ID()")->fetchColumn();
			} catch(PDOException $e) {
				//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                error_log($e->getMessage(), 0);
			}
		} else {
			$kwordId = $kword->id;
		}
		
		return $kwordId;
	}
	
	public static function setDeleted($id) {
		$sth = Ode_DBO::getInstance()->prepare("UPDATE " . DBO_Keyword::TABLE_NAME . " SET is_deleted = 1 WHERE id = :id");
		$sth->bindParam(":id", $id, PDO::PARAM_INT, 11);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			error_log($e->getMessage() . "\n\nLine: " . __LINE__ . "\nFile: " . __FILE__ . "\nMethod: " . __METHOD__ . "\nClass: " . __CLASS__, 1, APP_ADMIN_EMAIL);
		} catch(Exception $e) {
			error_log($e->getMessage() . "\n\nLine: " . __LINE__ . "\nFile: " . __FILE__ . "\nMethod: " . __METHOD__ . "\nClass: " . __CLASS__, 1, APP_ADMIN_EMAIL);
		}
		
		return $id;
	}
	
	public static function delete($id) {
		$sth = Ode_DBO::getInstance()->prepare("DELETE FROM " . DBO_Keyword::TABLE_NAME . " WHERE id = :id");
		$sth->bindParam(":id", $id, PDO::PARAM_INT, 11);
		
		try {
			$sth->execute();		
		} catch(PDOException $e) {
			//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
			error_log($e->getMessage() . "\nLine: " . __LINE__ . "\nFile: " . __FILE__ . "\nMethod: " . __METHOD__ . "\nObject: " . __CLASS__, 0);
		}
		
		return id;
	}
}