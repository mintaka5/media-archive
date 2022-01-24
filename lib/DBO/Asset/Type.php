<?php
class DBO_Asset_Type {
	const TABLE_NAME = "asset_types";
	const MODEL_NAME = "DBO_Asset_Type_Model";
	const COLUMNS = "a.id, a.name, a.title, a.is_active, a.mime_type";
	
	public static function getOneById($id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.id = " . Ode_DBO::getInstance()->quote($id, PDO::PARAM_INT) . "
			LIMIT 0,1
		")->fetchObject(self::MODEL_NAME);
	}
}