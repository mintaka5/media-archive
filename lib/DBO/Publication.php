<?php
class DBO_Publication {
	const TABLE_NAME = "publications";
	const MODEL_NAME = "DBO_Publication_Model";
	const COLUMNS = 'a.id,a.title,a.is_active';
	
	public static function add($title) {
		Ode_DBO::getInstance()->beginTransaction();
		
		$sth = Ode_DBO::getInstance()->prepare("
			INSERT INTO " . self::TABLE_NAME . " (title, is_active)
			VALUES (:title, 1)
		");
		$sth->bindValue(":title", trim($title), PDO::PARAM_STR);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
            error_log($e->getMessage(), 0);
			
			return false;
		}
		
		$id = Ode_DBO::getInstance()->query("SELECT LAST_INSERT_ID()")->fetchColumn();
		
		Ode_DBO::getInstance()->commit();
		
		return $id;
	}
	
	public static function getOneByTitle($title) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.title = " . Ode_DBO::getInstance()->quote($title, PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchObject(self::MODEL_NAME);
	}
	
	public static function getOneById($id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.id = " . Ode_DBO::getInstance()->quote($id, PDO::PARAM_INT) . "
			LIMIT 0,1
		")->fetchObject(self::MODEL_NAME);
	}
	
	public function getAllActive($order = 'title', $sort = 'ASC') {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.is_active = 1
			ORDER BY a." . $order . "
			" . $sort . "
		")->fetchAll(PDO::FETCH_CLASS, self::MODEL_NAME);
	}
}
?>