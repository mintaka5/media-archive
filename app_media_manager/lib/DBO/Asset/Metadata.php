<?php
class DBO_Asset_Metadata {
	const TABLE_NAME = "asset_metadata";
	const MODEL_NAME = "DBO_Asset_Metadata_Model";
	const COLUMNS = "a.id, a.metadata_name, a.metadata_value, a.asset_id, a.is_deleted, a.created, a.modified";
	
	const META_ORG_ID_NAME = "org_id";
	const META_NUM_VIEWS = "num_views";
	const META_RIGHTS = "rights";
	
	/**
	 * Retrieve a metadata object by name for an asset. If you want to
	 * to get multiple instances of one variable, set $is_single to false
	 * 
	 * @param string $name
	 * @param string $asset_id
	 * @param boolean $is_single
	 * @return mixed string|array|DBO_Asset_Metadata
	 */
	public static function get($name, $asset_id, $is_single = true) {
		$query = Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.metadata_name = " . Ode_DBO::getInstance()->quote($name, PDO::PARAM_STR) . "
			AND a.asset_id = " . Ode_DBO::getInstance()->quote($asset_id, PDO::PARAM_STR) . "
			AND a.is_deleted = 0	
		");
		
		if($is_single == true) {
			return $query->fetchObject(self::MODEL_NAME);
		} else {
			return $query->fetchAll(PDO::FETCH_CLASS, self::MODEL_NAME);
		}
		
		return false;
	}
	
	/**
	 * Checks to see if a particular metadata object exists for an asset
	 * 
	 * @param string $name
	 * @param string $asset_id
	 * @return mixed boolean|integer
	 */
	public static function exists($name, $asset_id) {
		$metadata = Ode_DBO::getInstance()->query("
			SELECT a.id
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.metadata_name = " . Ode_DBO::getInstance()->quote($name, PDO::PARAM_STR) . "
			AND a.asset_id = " . Ode_DBO::getInstance()->quote($asset_id, PDO::PARAM_STR) . "
			AND a.is_deleted = 0
		")->fetchColumn();

		if($metadata != false) {
			return $metadata;
		}
		
		return false;
	}
	
        /**
         * Checks to see if a medadata field with a certain value exists for an asset
         * @param string $name
         * @param mixed $value
         * @param string $asset_id
         * @return integer Metadata ID number
         */
	public static function valueExists($name, $value, $asset_id) {
		$metadata = Ode_DBO::getInstance()->query("
			SELECT a.id
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.metadata_name = " . Ode_DBO::getInstance()->quote($name, PDO::PARAM_STR) . "
			AND a.asset_id = " . Ode_DBO::getInstance()->quote($asset_id, PDO::PARAM_STR) . "
			AND a.is_deleted = 0
			AND a.metadata_value = " . Ode_DBO::getInstance()->quote($value, PDO::PARAM_STR) . "
		")->fetchColumn();
		
		if($metadata != false) {
			return $metadata;
		}
		
		return false;
	}
	
	/**
	 * Adds a new metadata object to the database for an asset.
	 * If metadata is a unique property, set to true, and it will attempt
	 * to update a pre-existing metadata object
	 * 
	 * @param string $name
	 * @param string $value
	 * @param string $asset_id
	 * @param boolean $is_unique
	 */
	public static function add($name, $value, $asset_id, $is_unique = false) {
		$exists = self::exists($name, $asset_id);
		
		if($exists != false && $is_unique == true) {
			/**
			 * If data exists and this is a unique field
			 * then edit instead of adding a new one
			 */
			self::edit($exists, $value);
		} else {
			/**
			 * add a new instance
			 */
			$sth = Ode_DBO::getInstance()->prepare("INSERT INTO " . self::TABLE_NAME . " (metadata_name, metadata_value, asset_id, is_deleted, created, modified) VALUES (
					:name, :value, :asset, 0, NOW(), NOW()
					)");
			$sth->bindParam(":name", $name, PDO::PARAM_STR, 45);
			$sth->bindParam(":value", $value, PDO::PARAM_STR, 255);
			$sth->bindParam(":asset", $asset_id, PDO::PARAM_STR, 50);
			
			try {
				$sth->execute();
			} catch(PDOException $e) {
				//Ode_Error::mail($e->getMessage(), __LINE__, __FILE__, APP_ADMIN_EMAIL);
				error_log($e->getMessage(), 0);
			} catch(PDOException $e) {
				//Ode_Error::mail($e->getMessage(), __LINE__, __FILE__, APP_ADMIN_EMAIL);
				error_log($e->getMessage(), 0);
			}
		}
		
		return;
	}
	
	public static function edit($id, $value) {
		$sth = Ode_DBO::getInstance()->prepare("UPDATE " . self::TABLE_NAME . " SET metadata_value = :value, modified = NOW() WHERE id = :id");
		$sth->bindParam(":value", $value, PDO::PARAM_STR, 255);
		$sth->bindParam(":id", $id, PDO::PARAM_INT, 11);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			//Ode_Error::mail($e->getMessage(), __LINE__, __FILE__, APP_ADMIN_EMAIL);
			error_log($e->getMessage(), 0);
		} catch(PDOException $e) {
			//Ode_Error::mail($e->getMessage(), __LINE__, __FILE__, APP_ADMIN_EMAIL);
			error_log($e->getMessage(), 0);
		}
		
		return;
	}
	
	public static function getAll($asset_id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS ."
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.asset_id = " . Ode_DBO::getInstance()->quote($asset_id, PDO::PARAM_STR) . "
		")->fetchAll(PDO::FETCH_CLASS, self::MODEL_NAME);
	}
	
	public static function removeByValue($name, $asset_id, $value) {
		$sth = Ode_DBO::getInstance()->prepare("DELETE FROM " . self::TABLE_NAME . " WHERE metadata_name = :name AND metadata_value = :value AND asset_id = :asset_id");
		$sth->bindParam(":name", $name, PDO::PARAM_STR, 45);
		$sth->bindParam(":value", $value, PDO::PARAM_STR);
		$sth->bindParam(":asset_id", $asset_id, PDO::PARAM_STR, 50);
		
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
        
        /**
         * Bulk update asset metadata derived from a array list of IDs
         * @param string $meta_key
         * @param mixed $value
         * @param string $asset_id
         * @return void
         */
        public static function bulkAdd($meta_key, $value, $asset_ids) {
            foreach($asset_ids as $id) {
                // if value and key exist add new one
                $exists = self::valueExists($meta_key, $value, $id);
                if($exists) {
                    self::edit($exists, $value);
                } else {
                    self::add($meta_key, $value, $id);
                }
            }
            
            return;
        }
        
        /**
         * Remove multiple metadata for an asset
         * @param string $meta_key
         * @param array $asset_ids Asset ID
         * @param mixed $value
         * @return void
         */
        public static function bulkRemoveByValue($meta_key, $asset_ids, $value) {
            foreach($asset_ids as $id) {
                self::removeByValue($meta_key, $id, $value);
            }
            
            return;
        }
}
?>