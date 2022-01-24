<?php
class DBO_Photographer {
	const TABLE_NAME = "photographers";
	const MODEL_NAME = "DBO_Photographer_Model";
	const SELECT_COLUMNS = "a.id,a.firstname,a.lastname,a.modified_by";
	
	public static function getOneById($id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::SELECT_COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a. id = " . Ode_DBO::getInstance()->quote($id, PDO::PARAM_INT) . "
			LIMIT 0,1
		")->fetchObject(self::MODEL_NAME);
	}
	
	public static function add($fname, $lname, $user_id) {
		Ode_DBO::getInstance()->beginTransaction();
		
		$sth = Ode_DBO::getInstance()->prepare("
			INSERT INTO " . self::TABLE_NAME . " (firstname, lastname, modified_by)
			VALUES (:fname, :lname, :user)
		");
		$sth->bindValue(":fname", trim($fname), PDO::PARAM_STR);
		$sth->bindValue(":lname", trim($lname), PDO::PARAM_STR);
		$sth->bindValue(":user", $user_id, PDO::PARAM_STR);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
            error_log($e->getMessage(), 0);
		}
		
		Ode_DBO::getInstance()->commit();
		
		return Ode_DBO::getInstance()->query("SELECT LAST_INSERT_ID()")->fetchColumn();
	}
}