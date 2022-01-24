<?php
class DBO_Properties {
	const TABLE_NAME = "properties";
	const MODEL_NAME = "DBO_Properties_Model";
	const COLUMNS = "a.id, a.machine_name, a.value, a.title, a.is_enabled, a.modified";
	
	const RIGHTS_PROPERTY_NAME = "rights";
        const FEATURED_GROUPS = 'featured_groups';
	
	public static function getOneById($id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.id = " . Ode_DBO::getInstance()->quote($id, PDO::PARAM_INT) . "
			LIMIT 0,1
		")->fetchObject();
	}
}