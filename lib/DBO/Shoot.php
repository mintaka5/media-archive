<?php
class DBO_Shoot {
	const TABLE_NAME = "shoots";
	const MODEL_NAME = "DBO_Shoot_Model";
	const COLUMNS = "a.id,a.shoot_name,a.title,a.description,a.shoot_date,a.is_active,a.created,a.modified,a.modified_by";
	
	public static function getOneById($id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.id = " . Ode_DBO::getInstance()->quote($id, PDO::PARAM_INT) . "
			LIMIT 0,1
		")->fetchObject(self::MODEL_NAME);
	}
}
?>