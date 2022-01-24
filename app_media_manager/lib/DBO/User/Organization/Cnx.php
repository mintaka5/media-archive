<?php
class DBO_User_Organization_Cnx {
	const TABLE_NAME = "user_organization_cnx";
	const MODEL_NAME = "DBO_User_Organization_Cnx_Model";
	const COLUMNS = "a.id, a.user_id, a.org_id";
	
	public static function getAllByUser($user_id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.user_id = " . Ode_DBO::getInstance()->quote($user_id, PDO::PARAM_STR) . "
		")->fetchAll(PDO::FETCH_CLASS, self::MODEL_NAME);
	}
	
	/**
	 * Return organization DB object if user is in that organization
	 * @param string $user_id
	 * @param integer $org_id
	 * @return mixed|boolean|DBO_Organization_Model
	 */
	public static function isInOrganization($user_id, $org_id) {
		$cnx = Ode_DBO::getInstance()->query("
			SELECT a.id
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.user_id = " . Ode_DBO::getInstance()->quote($user_id, PDO::PARAM_STR) . "
			AND a.org_id = " . Ode_DBO::getInstance()->quote($org_id, PDO::PARAM_INT) . "
			LIMIT 0,1
		")->fetchObject(DBO_Organization::MODEL_NAME);
		
		if($cnx !== false) {
			return true;
		}
		
		return false;
	}
	
	public static function getUserOrgIDs($user_id) {
		return Ode_DBO::getInstance()->query("
			SELECT a.org_id
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.user_id = " . Ode_DBO::getInstance()->quote($user_id, PDO::PARAM_STR) . "
		")->fetchAll(PDO::FETCH_COLUMN);
	}
	
	public static function removeUserFromOrg($user_id, $org_id) {
		$sth = Ode_DBO::getInstance()->prepare("DELETE FROM " . self::TABLE_NAME . " WHERE user_id = :user_id AND org_id = :org_id");
		$sth->bindParam(":user_id", $user_id, PDO::PARAM_STR, 50);
		$sth->bindParam(":org_id", $org_id, PDO::PARAM_INT, 11);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			error_log($e->getMessage(), 0);
		} catch(Exception $e) {
			error_log($e->getMessage(), 0);
		}
		
		return true;
	}
	
	public static function getOneByUserAndOrg($user_id, $org_id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.user_id = " . Ode_DBO::getInstance()->quote($user_id, PDO::PARAM_STR) . "
			AND a.org_id = " . Ode_DBO::getInstance()->quote($org_id, PDO::PARAM_INT) . "
		")->fetchObject(self::MODEL_NAME);
	}
	
	public static function addUserToOrg($user_id, $org_id) {
		$cnx = self::getOneByUserAndOrg($user_id, $org_id);
		
		if($cnx != false) {
			return false;
		}
		
		$sth = Ode_DBO::getInstance()->prepare("
			INSERT INTO " . self::TABLE_NAME . " (user_id, org_id)
			VALUES (:user_id, :org_id)
		");
		$sth->bindParam(":user_id", $user_id, PDO::PARAM_STR, 50);
		$sth->bindParam(":org_id", $org_id, PDO::PARAM_INT, 11);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			error_log($e->getMessage(), 0);
		} catch(Exception $e) {
			error_log($e->getMessage(), 0);
		}
	}
	
	public static function getAllUsersByOrg($org_id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			LEFT JOIN " . DBO_User::TABLE_NAME . " AS b ON (b.id = a.user_id)
			WHERE a.org_id = " . Ode_DBO::getInstance()->quote($org_id, PDO::PARAM_INT) . "
			AND b.is_deleted = 0
		")->fetchAll(PDO::FETCH_CLASS, self::MODEL_NAME);
	}
}