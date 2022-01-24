<?php
class DBO_Caption_Type {
	const FINAL_NAME = "final";
	const FEATURE_NAME = "feat";
	const GENERIC_NAME = "gen";
	const HISTORIC_NAME = "hist";
	const TABLE_NAME = "caption_types";
	const MODEL_NAME = "DBO_Caption_Type_Model";
	
	public static function getIdFromName($name) {
		return Ode_DBO::getInstance()->query("
			SELECT a.id
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.capn_name = " . Ode_DBO::getInstance()->quote($name, PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchColumn();
	}
}
?>