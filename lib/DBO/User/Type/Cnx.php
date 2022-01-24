<?php
class DBO_User_Type_Cnx {
	const TABLE_NAME = "user_type_cnx";
	const MODEL_NAME = "DBO_User_Type_Cnx_Model";
	
	const COLUMNS = "a.id, a.user_id, a.type_id";
	
	public static function update($user_id, $type_id) {
		$cnx = self::getOneByUser($user_id);
		
		if($cnx != false) {
			$sth = Ode_DBO::getInstance()->prepare("
				UPDATE " . self::TABLE_NAME . "
				SET
					type_id = :type
				WHERE id = :id
			");
			$sth->bindValue(":type", $type_id, PDO::PARAM_INT);
			$sth->bindValue(":id", $cnx->id, PDO::PARAM_INT);
			
			try {
				$sth->execute();
			} catch(PDOException $e) {
				//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                error_log($e->getMessage(), 0);
			}
		} else {
			$sth = Ode_DBO::getInstance()->prepare("
				INSERT INTO " . self::TABLE_NAME . " (user_id, type_id)
				VALUES (:user, :type)
			");
			$sth->bindValue(":user", $user_id, PDO::PARAM_STR);
			$sth->bindValue(":type", $type_id, PDO::PARAM_INT);
			
			try {
				$sth->execute();
			} catch(PDOException $e) {
				//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                error_log($e->getMessage(), 0);
			}
		}
		
		return;
	}
	
	public static function getOneByUser($user_id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.user_id = " . Ode_DBO::getInstance()->quote($user_id, PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchObject(self::MODEL_NAME);
	}
}
?>