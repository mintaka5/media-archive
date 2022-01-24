<?php
class DBO_Order_LineItem {
	const TABLE_NAME = "order_lineitems";
	const MODEL_NAME = "DBO_Order_LineItem_Model";
	const COLUMNS = "a.id,a.order_id,a.asset_id,a.is_approved,a.created";
	
	public static function getAllByOrder($order_id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.order_id = " . Ode_DBO::getInstance()->quote($order_id, PDO::PARAM_STR) . "
		")->fetchAll(PDO::FETCH_CLASS, self::MODEL_NAME);
	}
	
	public static function add($order_id, $asset_id) {
		if(!self::exists($order_id, $asset_id)) {
			$sth = Ode_DBO::getInstance()->prepare("
				INSERT INTO " . self::TABLE_NAME . " (order_id, asset_id, is_approved, created)
				VALUES (:order, :asset, 0, NOW())
			");
			$sth->bindValue(":order", $order_id, PDO::PARAM_STR);
			$sth->bindValue(":asset", $asset_id, PDO::PARAM_STR);
			
			try {
				$sth->execute();
			} catch(PDOException $e) {
				//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                error_log($e->getMessage(), 0);
				
				return false;
			}
		}
		
		return true;
	}
	
	public static function exists($order_id, $asset_id) {
		$lineitem = self::getOneByOrderAndAsset($order_id, $asset_id);
		
		if($lineitem != false) {
			return true;
		}
		
		return false;
	}
	
	public static function getOneByOrderAndAsset($order_id, $asset_id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.order_id = " . Ode_DBO::getInstance()->quote($order_id, PDO::PARAM_STR) . "
			AND a.asset_id = " . Ode_DBO::getInstance()->quote($asset_id, PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchObject(self::MODEL_NAME);
	}
	
	public static function deleteById($lineitem_id) {
		$sth = Ode_DBO::getInstance()->prepare("DELETE FROM " . self::TABLE_NAME . " WHERE id = :id");
		$sth->bindValue(":id", $lineitem_id, PDO::PARAM_INT);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
            error_log($e->getMessage(), 0);
		
			return false;
		}
		
		return true;
	}
	
	public static function deleteByOrder($order_id) {
		$sth = Ode_DBO::getInstance()->prepare("DELETE FROM " . self::TABLE_NAME . " WHERE order_id = :order");
		$sth->bindValue(":order", $order_id, PDO::PARAM_STR);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
            error_log($e->getMessage(), 0);
		
			return false;
		}
		
		return true;
	}
	
	public static function approve($lineitem_id, $is_approved = 1) {
		$sth = Ode_DBO::getInstance()->prepare("
			UPDATE " . self::TABLE_NAME . "
			SET
				is_approved = :appr
			WHERE id = :id
		");
		$sth->bindValue(":appr", $is_approved, PDO::PARAM_INT);
		$sth->bindValue(":id", $lineitem_id, PDO::PARAM_INT);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
            error_log($e->getMessage(), 0);
		
			return false;
		}
		
		return true;
	}
	
	public static function approveByAsset($asset_id, $is_approved = 1) {
		$sth = Ode_DBO::getInstance()->prepare("
			UPDATE " . self::TABLE_NAME . "
			SET
				is_approved = :appr
			WHERE asset_id = :asset
		");
		$sth->bindParam(":appr", $is_approved, PDO::PARAM_INT, 1);
		$sth->bindParam(":asset", $asset_id, PDO::PARAM_STR, 50);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
            error_log($e->getMessage(), 0);
		
			return false;
		}
		
		return true;
	}
}
?>