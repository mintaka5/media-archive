<?php
class DBO_Order {
	const TABLE_NAME = "orders";
	const MODEL_NAME = "DBO_Order_Model";
	const COLUMNS = "a.id,a.order_id,a.user_id,a.is_active,a.is_deleted,a.created";
	
	public static function getOneActiveByUser($user_id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.user_id = " . Ode_DBO::getInstance()->quote($user_id, PDO::PARAM_STR) . "
			AND is_deleted = 0
			AND is_active = 1
			LIMIT 0,1
		")->fetchObject(self::MODEL_NAME);
	}
	
	public static function add($user_id) {
		$uuid = Ode_DBO::getInstance()->query("SELECT UUID()")->fetchColumn();
		
		$sth = Ode_DBO::getInstance()->prepare("
			INSERT INTO " . self::TABLE_NAME . " (id, order_id, user_id, is_active, is_deleted, created)
			VALUES (:id, :order_id, :user_id, 1, 0, NOW())
		");
		$sth->bindValue(":id", $uuid, PDO::PARAM_STR);
		$sth->bindValue(":order_id", Util::simpleID(), PDO::PARAM_STR);
		$sth->bindValue(":user_id", $user_id, PDO::PARAM_STR);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
            error_log($e->getMessage(), 0);

			return false;
		}
		
		return $uuid;
	}
	
	public static function delete($order_id) {
		DBO_Order_LineItem::deleteByOrder($order_id);
		
		$sth = Ode_DBO::getInstance()->prepare("DELETE FROM " . self::TABLE_NAME . " WHERE id = :id");
		$sth->bindValue(":id", $order_id, PDO::PARAM_STR);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
            error_log($e->getMessage(), 0);

			return false;
		}
		
		return true;
	}
	
	public static function deactivate($order_id) {
		$sth = Ode_DBO::getInstance()->prepare("
			UPDATE " . self::TABLE_NAME . "
			SET
				is_active = 0
			WHERE id = :id
		");
		$sth->bindValue(":id", $order_id, PDO::PARAM_STR);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
            error_log($e->getMessage(), 0);

			return false;
		}
		
		return true;
	}
	
	public static function getOneById($order_id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.id = " . Ode_DBO::getInstance()->quote($order_id, PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchObject(self::MODEL_NAME);
	}
}
?>