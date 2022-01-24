<?php
class DBO_Container_Metadata {
	const TABLE_NAME = "container_metadata";
	const MODEL_NAME = "DBO_Container_Metadata_Model";
	const COLUMNS = "a.id, a.container_id, a.meta_key, a.meta_value, a.is_deleted";
	
	const META_ORG_ID_NAME = "org_id";
	const META_GROUP_ID_NAME = "group_id";
	
	public static function valueExists($meta_name, $meta_value, $container_id) {
		$m = Ode_DBO::getInstance()->query("
			SELECT a.id
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.container_id = " . Ode_DBO::getInstance()->quote($container_id, PDO::PARAM_STR) . "
			AND a.is_deleted = 0
			AND a.meta_key = " . Ode_DBO::getInstance()->quote($meta_name, PDO::PARAM_STR) . "
			AND a.meta_value = " . Ode_DBO::getInstance()->quote($meta_value, PDO::PARAM_STR) . "
		")->fetchColumn();
		
		if($m != false) {
			return $m;
		}
		
		return false;
	}
	
	public static function add($meta_name, $meta_value, $container_id, $is_unique = false) {
		$exists = self::exists($meta_name, $container_id);
		
		if($exists != false && $is_unique == true) {
			self::edit($exists, $meta_value);
		} else {
			$sth = Ode_DBO::getInstance()->prepare("
				INSERT INTO " . self::TABLE_NAME . " (container_id, meta_key, meta_value, is_deleted)
				VALUES (:container, :name, :value, 0)
			");
			$sth->bindParam(":container", $container_id, PDO::PARAM_STR, 50);
			$sth->bindParam(":name", $meta_name, PDO::PARAM_STR, 45);
			$sth->bindParam(":value", $meta_value, PDO::PARAM_STR);
			
			try {
				$sth->execute();
			} catch(PDOException $e) {
				error_log($e->getMessage(), 0);
				
				return false;
			} catch(PDOException $e) {
				error_log($e->getMessage(), 0);
				
				return false;
			}
		}
		
		return true;
	}
	
	public static function edit($meta_id, $meta_value) {
		$sth = Ode_DBO::getInstance()->prepare("UPDATE " . self::TABLE_NAME . "  SET meta_value = :value WHERE id = :id");
		$sth->bindParam(":value", $meta_value, PDO::PARAM_STR);
		$sth->bindParam(":id", $meta_id, PDO::PARAM_INT, 11);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			error_log($e->getMessage(), 0);
		} catch(PDOException $e) {
			error_log($e->getMessage(), 0);
		}
		
		return true;
	}
	
	public static function exists($meta_name, $container_id) {
		$m = Ode_DBO::getInstance()->query("
			SELECT a.id
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.is_deleted = 0
			AND a.meta_key = " . Ode_DBO::getInstance()->quote($meta_name, PDO::PARAM_STR) . "
			AND a.container_id = " . Ode_DBO::getInstance()->quote($container_id, PDO::PARAM_STR) . "
		")->fetchColumn();
		
		if($m != false) {
			return $m;
		}
		
		return false;
	}
	
	public static function get($meta_name, $container_id, $is_single = true) {
		$q = Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.meta_key = " . Ode_DBO::getInstance()->quote($meta_name, PDO::PARAM_STR) . "
			AND a.container_id = " . Ode_DBO::getInstance()->quote($container_id, PDO::PARAM_STR) . "
			AND a.is_deleted = 0
		");
		
		if($is_single == true) {
			return $q->fetchObject(self::MODEL_NAME);
		} else {
			return $q->fetchAll(PDO::FETCH_CLASS, self::MODEL_NAME);
		}
		
		return false;
	}
	
	public static function getAll($container_id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.container_id = " . Ode_DBO::getInstance()->quote($container_id, PDO::PARAM_STR) . " 
		")->fetchAll(PDO::FETCH_CLASS, self::MODEL_NAME);
	}
	
	public static function delete($container_id, $meta_key, $meta_value) {
		$sth = Ode_DBO::getInstance()->prepare("DELETE FROM " . DBO_Container_Metadata::TABLE_NAME . " WHERE container_id = :container AND meta_key = :key AND meta_value = :val");
		$sth->bindParam(":container", $container_id, PDO::PARAM_STR, 50);
		$sth->bindParam(":key", $meta_key, PDO::PARAM_STR, 45);
		$sth->bindParam(":val", $meta_value, PDO::PARAM_STR);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			error_log($e->getMessage(), 0);
			
			return false;
		} catch(PDOException $e) {
			error_log($e->getMessage(), 0);
			
			return false;
		}
		
		return true;
	}
}
?>