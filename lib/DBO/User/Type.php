<?php
class DBO_User_Type {
	const TABLE_NAME = "user_types";
	const MODEL_NAME = "DBO_User_Type_Model";
	const COLUMNS = "a.id,a.type_name,a.title,a.is_deleted";
	
	public static function getOneByUser($user_id) {
		$result = Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM user_type_cnx AS cnx
			LEFT JOIN user_types AS a ON (a.id = cnx.type_id)
			WHERE cnx.user_id = " . Ode_DBO::getInstance()->quote($user_id, PDO::PARAM_INT) . "
			LIMIT 0,1
		")->fetchObject(self::MODEL_NAME);
		
		return $result;
	}
	
	public static function getAllActive($order = "title", $sort = "ASC") {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.is_deleted = 0
			ORDER BY a." . $order . " "
			. $sort. "
		")->fetchAll(PDO::FETCH_CLASS, self::MODEL_NAME);
	}
	
	/**
	 * retrieve all active user types from database by role/user type
	 * @param string $user_id
	 * @return DBO_User_Type_Model[]|false
	 */
	public static function getAllByUserType($user_id) {
		$user = DBO_User::getOneById($user_id);
		
		switch($user->type()->type_name) {
			default: 
				$types = false; 
				break;
			case DBO_User_Model::ADMIN_TYPE:
				$types = self::getAllActive();
				break;
			case DBO_User_Model::MANAGER_TYPE:
				$types = self::getManagersActive();
				break;
		}
		
		return $types;
	}
	
	/**
	 * Retrieve all respective user types/roles for the Manager's role/user type
	 * @param string $order
	 * @param string $sort
	 * @return DBO_User_Type_Model[]|false
	 */
	public static function getManagersActive($order = 'title', $sort = 'ASC') {
		$restricted = array(DBO_User_Model::ADMIN_TYPE);
		$restricted = Util::dbQuoteListItems($restricted);
		
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.is_deleted = 0
			AND a.type_name NOT IN (" . implode(",", $restricted) . ")
			ORDER BY a." . $order . " "
			. $sort. "
		")->fetchAll(PDO::FETCH_CLASS, self::MODEL_NAME);
	}
}