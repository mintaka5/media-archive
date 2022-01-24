<?php
class DBO_Organization {
	const TABLE_NAME = "organizations";
	const MODEL_NAME = "DBO_Organization_Model";
	const COLUMNS = "a.id, a.org_name, a.title, a.is_deleted, a.created";
	
	public static function getOneById($org_id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.id = " . Ode_DBO::getInstance()->quote($org_id, PDO::PARAM_INT) . "
			LIMIT 0,1
		")->fetchObject(self::MODEL_NAME);
	}
	
	public static function getAllActive($order = "title", $sort = "ASC") {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.is_deleted = 0
			ORDER BY a." . $order . "
			" . $sort . "
		")->fetchAll(PDO::FETCH_CLASS, self::MODEL_NAME);
	}
	
	public static function getOneByName($org_name) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.org_name = " . Ode_DBO::getInstance()->quote($org_name, PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchObject(self::MODEL_NAME);
	}
	
	/**
	 * Add a new organization to the database
	 * @param string $org_name
	 * @param string $title
	 * @return boolean|integer
	 */
	public static function add($org_name, $title) {
		Ode_DBO::getInstance()->beginTransaction();
		
		$sth = Ode_DBO::getInstance()->prepare("
			INSERT INTO " . self::TABLE_NAME . " (org_name, title, is_deleted, created)
			VALUES (:org_name, :title, 0, NOW())
		");
		$sth->bindParam(":org_name", $org_name, PDO::PARAM_STR, 45);
		$sth->bindParam(":title", $title, PDO::PARAM_STR, 45);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			error_log($e->getMessage(), 0);
			
			return false;
		} catch(Exception $e) {
			error_log($e->getMessage(), 0);
			
			return false;
		}
		
		$new_id = Ode_DBO::getInstance()->query("SELECT LAST_INSERT_ID()")->fetchColumn();
		
		Ode_DBO::getInstance()->commit();
		
		return $new_id;
	}
	
	public static function delete($org_id) {
		$sth = Ode_DBO::getInstance()->prepare("DELETE FROM " . self::TABLE_NAME . " WHERE id = :id");
		$sth->bindParam(":id", $org_id, PDO::PARAM_INT, 11);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			error_log($e->getMessage(), 0);
				
			return false;
		} catch(Exception $e) {
			error_log($e->getMessage(), 0);
				
			return false;
		}
		
		return true;
	}
	
	public static function nameExists($org_name) {
		$org_name = Ode_DBO::getInstance()->query("
			SELECT a.org_name
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.org_name = " . Ode_DBO::getInstance()->quote($org_name, PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchColumn();
		
		if($org_name != false) {
			return true;
		}
		
		return false;
	}
	
	public static function generateName($str) {
		$new_str = preg_replace("/[\n\r\t\s\W]+/", "", $str);
		$new_str = substr(strtolower($new_str), 0, 8) . rand(1, 1000);
		
		if(self::nameExists($new_str)) {
			self::generateName($str);
		} else {
			return $new_str;
		}
	}
        
        public static function updateTitle($id, $title) {
            $sth = Ode_DBO::getInstance()->prepare("
                UPDATE " . self::TABLE_NAME . "
                SET title = :title
                WHERE id = :id
            ");
            $sth->bindParam(":title", $title, PDO::PARAM_STR, 45);
            $sth->bindParam(":id", $id, PDO::PARAM_INT, 11);
            
            try {
                $sth->execute();
            } catch(Exception $e) {
                error_log($e->getTraceAsString(), 0);
                
                return false;
            }
            
            return true;
        }
}
?>