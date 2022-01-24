<?php
class DBO_Asset {
	const TABLE_NAME = "assets";
	const MODEL_NAME = "DBO_Asset_Model";
	const COLUMNS = "a.id,a.public_id,a.type_id,a.filename,a.title,a.caption,a.description,a.photographer_id,a.shoot_id, a.credit,a.location,a.lat,a.lng,a.is_active,a.is_deleted,a.created,a.modified,a.modified_by";
	
	public static function getAllByContainer($id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM asset_group_cnx AS group_cnx
			LEFT JOIN group_container_cnx AS container_cnx ON (container_cnx.group_id = group_cnx.group_id)
			LEFT JOIN assets AS a ON (a.id = group_cnx.asset_id)
			WHERE container_cnx.container_id = " . Ode_DBO::getInstance()->quote($id, PDO::PARAM_STR) . "
			AND a.is_deleted = 0
		")->fetchAll(PDO::FETCH_CLASS, "DBO_Asset_Model");
	}
	
	public static function getAllPublicByContainer($container_id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . " 
			FROM view_publicContainerAssets 
			WHERE container_id = " . Ode_DBO::getInstance()->quote($container_id, PDO::PARAM_STR) . "
		")->fetchAll(PDO::FETCH_CLASS, self::MODEL_NAME);
	}
	
	public static function getOneById($id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.id = " . Ode_DBO::getInstance()->quote($id, PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchObject(self::MODEL_NAME);
	}
	
	public static function getOneByPublicId($id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.public_id = " . Ode_DBO::getInstance()->quote($id, PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchObject(self::MODEL_NAME);
	}
	
	public static function getAllByGroup($group_id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM asset_group_cnx AS cnx
			LEFT JOIN assets AS a ON (a.id = cnx.asset_id)
			WHERE cnx.group_id = " . Ode_DBO::getInstance()->quote($group_id, PDO::PARAM_STR) . "
			AND a.is_deleted = 0
		")->fetchAll(PDO::FETCH_CLASS, "DBO_Asset_Model");
	}
	
	public static function getActiveByGroup($group_id) {
		return Ode_DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . DBO_Asset_Group_Cnx::TABLE_NAME . " AS cnx
			LEFT JOIN " . DBO_Asset::TABLE_NAME . " AS a ON (a.id = cnx.asset_id)
			WHERE cnx.group_id = " . Ode_DBO::getInstance()->quote($group_id, PDO::PARAM_STR) . "
			AND a.is_deleted = 0
			AND a.is_active = 1
		")->fetchAll(PDO::FETCH_CLASS, "DBO_Asset_Model");
	}
	
	public static function addUpload($filename, $mime_type, $user_id) {
		$uuid = UUID::get();
		
		$typeId = Ode_DBO::getInstance()->query("
			SELECT a.id 
			FROM asset_types AS a 
			WHERE a.mime_type = " . Ode_DBO::getInstance()->quote($mime_type, PDO::PARAM_STR) . " 
			LIMIT 0,1
		")->fetchColumn();
		
		$sth = Ode_DBO::getInstance()->prepare("
			INSERT INTO " . self::TABLE_NAME . " (id, public_id, type_id, filename, title, is_active, created, modified, modified_by)
			VALUES (:id, :public_id, :type_id, :filename, :title, 0, NOW(), NOW(), :modified_by)
		");
		$sth->bindValue(":id", $uuid, PDO::PARAM_STR);
        $sth->bindValue(":public_id", Util::simpleID(), PDO::PARAM_STR);
		$sth->bindValue(":type_id", $typeId, PDO::PARAM_INT);
		$sth->bindValue(":filename", $filename, PDO::PARAM_STR);
		$sth->bindValue(":title", basename($filename), PDO::PARAM_STR);
		$sth->bindValue(":modified_by", $user_id, PDO::PARAM_STR);
		
		try {
			$sth->execute();
		} catch (PDOException $e) {
            error_log($e->getMessage(), 0);
        }
		
		return $uuid;
	}
	
	/**
	 * Assign an asset to a user's related organizations
	 * @param string $asset_id
	 * @param string $user_id
	 */
	public static function assignOrganizationsByUser($asset_id, $user_id) {
		$orgs = DBO_User_Organization_Cnx::getAllByUser($user_id);
		foreach($orgs as $org) {
			/**
			 * Only assign if not already assigned
			 */
			if(DBO_Asset_Metadata::valueExists(DBO_Asset_Metadata::META_ORG_ID_NAME, $org->org_id, $asset_id) == false) {
				DBO_Asset_Metadata::add(DBO_Asset_Metadata::META_ORG_ID_NAME, $org->org_id, $asset_id);
			}
		}
	}
	
	public static function addFullUpload($filename, $mime_type, $user_id) {
		$assetId = self::addUpload($filename, $mime_type, $user_id);
		
		/**
		 * Get user orgs and assign all to asset's metadata org_id
		 */
		//self::assignOrganizationsByUser($assetId, $user_id);
		
		$metadata = new Metadata_XMP(IMAGE_STORAGE_PATH . $filename);
		$keywords = $metadata->keywords();
		
		/**
		 * Keywords must be in the form of an array list
		 */
		$kids = array();
		if(is_array($keywords)) {
			// process keywords
			$kids = DBO_Keyword::bulkAdd($keywords);
		} elseif (is_string($keywords)) {
			/**
			 * Sometimes the image file only gives us a keyword as just a string, handle it
			 * @var array
			 */
			$kids = DBO_Keyword::bulkAdd(array($keywords));
		}
		
		if(!empty($kids)) {
			// attach keywords to asset
			foreach ($kids as $kid) {
				DBO_Keyword_Asset_Cnx::assignById($assetId, $kid);
			}
		}
		
		// update asset's description, and final caption
		$desc = $metadata->description();
		if($desc) {
			$sth = Ode_DBO::getInstance()->prepare("UPDATE " . DBO_Asset::TABLE_NAME . " SET description = :desc WHERE id = :id");
			$sth->bindValue(":desc", $desc, PDO::PARAM_STR);
			$sth->bindValue(":id", $assetId, PDO::PARAM_STR);
			
			try {
				$sth->execute();
			} catch (PDOException $e) {
                error_log($e->getMessage(), 0);
            }
			
			$caption_id = DBO_Caption::assign($assetId, $desc, DBO_Caption_Type::getIdFromName('final'), $user_id);
		}
		
		/**
		 * 
		 * Update new asset's copyright statement, if it is available.
		 * @var string
		 */
        $credit = $metadata->creator();
        if($credit) {
            $sth = Ode_DBO::getInstance()->prepare("UPDATE " . DBO_Asset::TABLE_NAME . " SET credit = :cred WHERE id = :id");
            $sth->bindValue(":cred", $credit, PDO::PARAM_STR);
            $sth->bindValue(":id", $assetId, PDO::PARAM_STR);

            try {
            	$sth->execute();
            } catch (PDOException $e) {
                error_log($e->getMessage(), 0);
            }
         }
                
		/**
		 * 
		 * Update asset's geographical coordinates, if available. 
		 * @var object
		 */
		$coordinates = $metadata->coordinates();
		if($coordinates) {
			$sth = Ode_DBO::getInstance()->prepare("UPDATE " . DBO_Asset::TABLE_NAME . " SET lat = :lat, lng = :lng WHERE id = :id");
			$sth->bindValue(":lat", $coordinates->lat, PDO::PARAM_STR);
			$sth->bindValue(":lng", $coordinates->lng, PDO::PARAM_STR);
			$sth->bindValue(":id", $assetId, PDO::PARAM_STR);
			
			try {
				$sth->execute();
			} catch (PDOException $e) {
                error_log($e->getMessage(), 0);
            }
		}
                
                /**
                 * Update create date from the images metadata
                 * @var Date
                 */
                $createDate = $metadata->created();
                if($createDate != false) {
                    $sth = Ode_DBO::getInstance()->prepare("UPDATE ".self::TABLE_NAME." SET created = :created WHERE id = :id");
                    $sth->bindValue(":created", $createDate->getDate(), PDO::PARAM_STR);
                    $sth->bindValue(":id", $assetId, PDO::PARAM_STR);
                    
                    try {
                            $sth->execute();
                    } catch (PDOException $e) {
                        error_log($e->getMessage(), 0);
                    }
                }
                
                /**
                 * Update title if available from XMP
                 */
                $title = $metadata->title();
                if($title != false) {
                    $sth = Ode_DBO::getInstance()->prepare("UPDATE ".self::TABLE_NAME." SET title = :title WHERE id = :id");
                    $sth->bindValue(":title", $title, PDO::PARAM_STR);
                    $sth->bindValue(":id", $assetId, PDO::PARAM_STR);
                    
                    try {
                            $sth->execute();
                    } catch (PDOException $e) {
                        error_log($e->getMessage(), 0);
                    }
                }
		
		return $assetId;
	}
	
	public static function filenameExists($filename) {
		$id = Ode_DBO::getInstance()->query("
			SELECT a.id
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.filename = " . Ode_DBO::getInstance()->quote($filename, PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchColumn();
		
		if($id != false) {
			return true;
		}
		
		return false;
	}
	
	public static function generateFilename($original) {
		$month = date('m');
		$year = date('Y');
		$day = date('d');
		$rel_path = $year . DIRECTORY_SEPARATOR . $month . DIRECTORY_SEPARATOR . $day . DIRECTORY_SEPARATOR;
		
		/**
		 * Sanitize filename string and prefix the reltive filesystem path
		 * @var string
		 */
		$pathname = pathinfo($original);
		$original = $rel_path . preg_replace("/[^_\w\d\-]+/i", "_", $pathname['filename']) .".". $pathname['extension'];
		
		if(self::filenameExists($original)) {
			preg_match("/(.+)\.(.{3,4})/i", basename($original), $matches);
			
			$original = self::generateFilename($matches[1] . "_" . chr(rand(97, 122)) . rand(1, 99) . "." . $matches[2]);
		}
		
		//Util::debug($original);
		return $original;
	} 
	
	/**
	 * 
	 * Sets the delete status of asset to ON
	 * so that we don't permanently get rid of assets,
	 * in case we need to get them back. Relationships in the
	 * database will be permanetly erased though.
	 * @param string $id
	 */
	public static function delete($id) {
		$sth = Ode_DBO::getInstance()->prepare("
			UPDATE " . self::TABLE_NAME . "
			SET
				is_deleted = 1,
				modified = NOW(),
				modified_by = :user
			WHERE id = :id
		");
		$sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
		$sth->bindValue(":id", $id, PDO::PARAM_STR);
		
		try {
			$sth->execute();
			
			/**
			 * Remove published status
			 */
			DBO_Asset_Published::deleteByAsset($id);
		} catch(PDOException $e) {
			error_log($e->getMessage(), 0);
		}
		
		return;
	}
	
	public static function approve($asset_id, $approval = true) {
		$appr = ($approval == true) ? 1 : 0;
		
		$sth = Ode_DBO::getInstance()->prepare("
			UPDATE " . self::TABLE_NAME . "
			SET
				is_active = :appr,
				modified = NOW(),
				modified_by = :user
			WHERE id = :id
		");
		$sth->bindValue(":appr", $appr, PDO::PARAM_INT);
		$sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
		$sth->bindValue(":id", $asset_id, PDO::PARAM_STR);
		
		try {
			$sth->execute();
		} catch (PDOException $e) {
            error_log($e->getMessage(), 0);
		}
		
		return ($appr == 1) ? 0 : 1;
	}
	
	public static function approveInGroup($group_id, $approval = true) {
		$appr = ($approval == true) ? 1 : 0;
		
		$assets = self::getAllByGroup($group_id);
		
		if($assets != false) {
			foreach($assets as $asset) {
				self::approve($asset->id, $appr);
			}
		}
		
		return $appr;
	}
	
	public static function getNumActiveByGroup($group_id) {
		$sql = "SELECT COUNT(*)
				FROM view_publicGroupImages AS a 
				WHERE a.group_id = " . Ode_DBO::getInstance()->quote($group_id, PDO::PARAM_STR) . "";
		
		return Ode_DBO::getInstance()->query($sql)->fetchColumn();
	}
	
	public static function getNumTotalByGroup($group_id) {
		return Ode_DBO::getInstance()->query("
			SELECT COUNT(*)
			FROM " . DBO_Asset_Group_Cnx::TABLE_NAME . " AS a
			LEFT JOIN " . self::TABLE_NAME . " AS b ON (b.id = a.asset_id)
			WHERE b.is_deleted = 0
			AND a.group_id = " . Ode_DBO::getInstance()->quote($group_id, PDO::PARAM_STR) . "
		")->fetchColumn();
	}
	
	/**
	 * Get total number of non-deleted assets in archive
	 * @return integer
	 */
	public static function getNumTotal() {
		return Ode_DBO::getInstance()->query("
				SELECT COUNT(*)
				FROM assets AS a
				WHERE a.is_deleted = 0
		")->fetchColumn();
	}
}