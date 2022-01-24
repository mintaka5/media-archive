<?php
class DBO_User {
	const MODEL_NAME = "DBO_User_Model";
	const TABLE_NAME = "users";
	const COLUMNS = "a.id,a.username,a.password,a.email,a.firstname,a.lastname,a.is_active,a.is_deleted,a.created,a.modified";
	
	public static function getOneById($id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.id = " . Ode_DBO::getInstance()->quote($id, PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchObject("DBO_User_Model");
	}
	
	/**
	 * Retrive all users assigned to a user's organization
	 * If it is the admin provide all users
	 * @param string $user_id
	 * @return DBO_User_Model[]|false
	 */
	public function getAllByUserOrgs($user_id) {
		$user = self::getOneById($user_id);
		
		if($user->type()->type_name == DBO_User_Model::ADMIN_TYPE) {
			$sql = "SELECT " . DBO_User::COLUMNS . "
					FROM " . DBO_User::TABLE_NAME . " AS a
					WHERE a.is_deleted = 0
					ORDER BY a.lastname
					ASC";
		} else {
			$org_ids = Util::dbQuoteListItems(DBO_User_Organization_Cnx::getUserOrgIDs($user->id));
			$org_ids = (!empty($org_ids)) ? implode(",", $org_ids) : "''";
			
			$sql = "SELECT " . DBO_User::COLUMNS . "
					FROM " . DBO_User::TABLE_NAME . " AS a
					LEFT JOIN " . DBO_User_Organization_Cnx::TABLE_NAME . " AS b ON (b.user_id = a.id)
					LEFT JOIN " . DBO_User_Type_Cnx::TABLE_NAME . " AS c ON (c.user_id = a.id)
					LEFT JOIN " . DBO_User_Type::TABLE_NAME . " AS d ON (d.id = c.type_id)
					WHERE a.is_deleted = 0
					AND b.org_id IN (" . $org_ids . ")
					AND d.type_name != " . Ode_DBO::getInstance()->quote(DBO_User_Model::ADMIN_TYPE, PDO::PARAM_STR) . "
					GROUP BY a.id
					ORDER BY a.lastname
					ASC";
		}
		
		return Ode_DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_CLASS, self::MODEL_NAME);
	}
	
	public static function delete($user_id) {
		$sth = Ode_DBO::getInstance()->prepare("
			UPDATE " . self::TABLE_NAME . "
			SET
				is_deleted = 1,
				modified = NOW()
			WHERE id = :user
		");
		$sth->bindValue(":user", $user_id, PDO::PARAM_STR);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
            error_log($e->getMessage(), 0);
		}
		
		return;
	}
	
	public static function getOneByUsername($username) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.username = " . Ode_DBO::getInstance()->quote($username, PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchObject(self::MODEL_NAME);
	}
	
	public static function registerGuest(stdClass $user) {
		$uuid = Ode_DBO::getInstance()->query("SELECT UUID()")->fetchColumn();
		
		$sth = Ode_DBO::getInstance()->prepare("
			INSERT INTO " . self::TABLE_NAME . " (id, username, password, email, firstname, lastname, is_active, is_deleted, created, modified)
			VALUES (:id, :username, MD5(:password), :email, :firstname, :lastname, 1, 0, NOW(), NOW())
		");
		$sth->bindParam(":id", $uuid, PDO::PARAM_STR, 50);
		$sth->bindParam(":username", $user->username, PDO::PARAM_STR, 45);
		$sth->bindParam(":password", $user->password, PDO::PARAM_STR, 45);
		$sth->bindParam(":email", $user->email, PDO::PARAM_STR, 45);
		$sth->bindParam(":firstname", $user->firstname, PDO::PARAM_STR, 45);
		$sth->bindParam(":lastname", $user->lastname, PDO::PARAM_STR, 45);
		
		try {
			$sth->execute();
		} catch(Exception $e) {
			//echo $e->getMessage();
            error_log($e->getMessage(), 0);
			
			return false;
		} catch(PDOException $pe) {
			//echo $pe->getMessage();
            error_log($pe->getMessage(), 0);
			
			return false;
		}
		
		return $uuid;
	}
	
	public static function getAllByTypeName($type_name) {
		return Ode_DBO::getInstance()->query("
			SELECT b.*
			FROM " . DBO_User_Type_Cnx::TABLE_NAME . " AS a
			LEFT JOIN " . DBO_User::TABLE_NAME . " AS b ON (b.id = a.user_id)
			LEFT JOIN " . DBO_User_Type::TABLE_NAME . " AS c ON (c.id = a.type_id)
			WHERE c.type_name = " . Ode_DBO::getInstance()->quote($type_name, PDO::PARAM_STR) . "
			AND b.is_deleted = 0
		")->fetch(PDO::FETCH_CLASS, self::MODEL_NAME);
	}
}