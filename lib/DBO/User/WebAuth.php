<?php
class DBO_User_WebAuth {
	const TABLE_NAME = "user_webauths";
	const MODEL_NAME = "DBO_User_WebAuth_Model";
	const COLUMNS = "a.id, a.campusid, a.user_id, a.created";
	
	public static function getOneByCampusId($campus_id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.campusid = " . Ode_DBO::getInstance()->quote($campus_id, PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchObject(self::MODEL_NAME);
	}
	
	public static function add($campus_id, $user_id) {
		$sth = Ode_DBO::getInstance()->prepare("
			INSERT INTO " . self::TABLE_NAME . " (campusid, user_id, created)
			VALUES (:campusid, :user_id, NOW()) 
		");
		$sth->bindParam(":campusid", $campus_id, PDO::PARAM_STR, 45);
		$sth->bindParam(":user_id", $user_id, PDO::PARAM_STR, 50);
		
		try {
			$sth->execute();
		} catch(Exception $e) {
			//echo $e->getMessage();
            error_log($e->getMessage(), 0);
			
			return false;
		} catch(PDOException $pe) {
			//echo $pe->getMessage();
            error_log($e->getMessage(), 0);
			
			return false;
		}
	}
}
?>