<?php
class DBO_Container {
	const TABLE_NAME = "containers";
	const MODEL_NAME = "DBO_Container_Model";
	const COLUMNS = "a.id, a.title, a.description, a.is_approved, a.is_deleted, a.created, a.modified, a.modified_by";
	
	public static function assignOrganizationsByUser($container_id, $user_id) {
		$orgs = DBO_User_Organization_Cnx::getAllByUser($user_id);
		
		foreach($orgs as $org) {
			if(DBO_Container_Metadata::valueExists(DBO_Container_Metadata::META_ORG_ID_NAME, $org->org_id, $container_id) == false) {
				DBO_Container_Metadata::add(DBO_Container_Metadata::META_ORG_ID_NAME, $org->org_id, $container_id);
			}
		}
		
		return true;
	}
	
	public static function getOneById($id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.id = " . Ode_DBO::getInstance()->quote($id, PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchObject(self::MODEL_NAME);
	}
	
	public static function getGroups($container_id) {
		$m = DBO_Container_Metadata::get(DBO_Container_Metadata::META_GROUP_ID_NAME, $container_id, false);
		
		if($m != false) {
			$coll = new ArrayObject();
			
			foreach($m as $md) {
				$group = DBO_Group::getOneById($md->meta_value);
				$coll->append($group);
			}
			
			return $coll->getArrayCopy();
		}
		
		return false;
	}
	
	public static function getAll($order = "title", $sort = "ASC") {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.is_deleted = 0
			ORDER BY a." . $order . "
			" . $sort . "
		")->fetchAll(PDO::FETCH_CLASS, self::MODEL_NAME);
	}
	
	/**
	 * Get all containers by the organizations a user belongs to
	 * @param string $user_id User ID
	 * @return DBO_Container_Model[]
	 */
	public static function getAllByUserOrgs($user_id) {
		$user = DBO_User::getOneById($user_id);
		
		if($user->type()->type_name == DBO_User_Model::ADMIN_TYPE) {
			$containers = self::getAll();
		} else {
			$org_ids = Util::dbQuoteListItems(DBO_User_Organization_Cnx::getUserOrgIDs($user->id));
			$org_ids = (!empty($org_ids)) ? implode(",", $org_ids) : "''";
			
			$sql = "SELECT " . self::COLUMNS . "
					FROM " . self::TABLE_NAME . " AS a
					LEFT JOIN " . DBO_Container_Metadata::TABLE_NAME . " AS b ON (b.container_id = a.id)
					WHERE a.is_deleted = 0
					AND b.meta_key = " . Ode_DBO::getInstance()->quote(DBO_Container_Metadata::META_ORG_ID_NAME, PDO::PARAM_STR) . "
					AND b.meta_value IN (" . $org_ids . ")
					GROUP BY a.id";
			$containers = Ode_DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_CLASS, self::MODEL_NAME);
		}
		
		return $containers;
	}
	
	/**
	 * Retrieve all public-facing containers/collections
	 * Are approved/active; are not deleted;
	 * and must at least one group/set assigned to it
	 * 
	 * @param string $order
	 * @param string $sort
	 * @return DBO_Container_Model
	 */
	public static function getAllPublic($order = "title", $sort = 'ASC', $limit = false) {
		$sql = "SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.is_deleted = 0
			AND a.is_approved = 1
			AND (SELECT COUNT(*) 
				FROM " . DBO_Container_Metadata::TABLE_NAME . " 
				WHERE meta_key = '" . DBO_Container_Metadata::META_GROUP_ID_NAME . "'
				AND container_id = a.id) > 0
			ORDER BY a." . $order . "
			" . $sort . "";
		
		if($limit != false) {
			$sql .= " LIMIT 0," . $limit;
		}
		
		return Ode_DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_CLASS, self::MODEL_NAME);
	}
	
	public static function remove($container_id) {
		//$sth = Ode_DBO::getInstance()->prepare("DELETE FROM ". self::TABLE_NAME . " WHERE id = :id");
		$sth = Ode_DBO::getInstance()->prepare("UPDATE " . self::TABLE_NAME . " SET is_deleted = 1 WHERE id = :id");
		$sth->bindParam(":id", $container_id, PDO::PARAM_STR, 50);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			error_log($e->getTraceAsString());
			
			return false;
		} catch(Exception $e) {
			error_log($e->getTraceAsString());
			
			return false;
		}
		
		return true;
	}
	
	public static function setApproval($container_id, $approval) {
		$sth = Ode_DBO::getInstance()->prepare("
			UPDATE " . self::TABLE_NAME . "
			SET
				is_approved = :appr
			WHERE id = :id
		");
		$sth->bindParam(":appr", $approval, PDO::PARAM_INT, 1);
		$sth->bindParam(":id", $container_id, PDO::PARAM_STR, 50);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			error_log($e->getTraceAsString());
				
			return false;
		} catch(Exception $e) {
			error_log($e->getTraceAsString());
				
			return false;
		}
		
		return true;
	}
}
?>