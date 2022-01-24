<?php
class AssetManager {
	const SESSION_ASSETS = "edit_assets";
	
	private static $_instance;
	
	private $group;
	
	public function __construct() {
		self::$_instance = $this;
		
		if(!isset($_SESSION[self::SESSION_ASSETS])) {
			$group_id = $this->createGroup();
			
			$_SESSION[self::SESSION_ASSETS] = $group_id;
		}
		
		$this->setGroup();
	}
	
	private function setGroup() {
		$group = DBO_Group::getOneById($_SESSION[self::SESSION_ASSETS]);
		
		if(!$group) {
			$this->clear();
			
			$group_id = $this->createGroup();
			
			$_SESSION[self::SESSION_ASSETS] = $group_id;
			
			$group = DBO_Group::getOneById($_SESSION[self::SESSION_ASSETS]);
		}
		
		$this->group = $group;
	}
	
	/**
	 * @return DBO_Group_Model
	 */
	public function getGroup() {
		return $this->group;
	}
	
	private function createGroup() {
		$id = Ode_DBO::getInstance()->query("SELECT UUID()")->fetchColumn();
		
		$sth = Ode_DBO::getInstance()->prepare("
					INSERT INTO groups (id, title, is_approved, is_deleted, created, modified, modified_by)
					VALUES (:id, 'Batch Edit', 0, 1, NOW(), NOW(), :modified_by)
				");
		$sth->bindValue(":id", $id, PDO::PARAM_STR);
		$sth->bindValue(":modified_by", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			error_log($e->getTraceAsString(), 0);
		} catch(Exception $e) {
			error_log($e->getTraceAsString(), 0);
		}
		
		return $id;
	}
	
	public static function getInstance() {
		return self::$_instance;
	}
	
	/**
	 * Add asset ID to session to edit assets
	 * If already in session, return false
	 * @param string $asset_id
	 * @return boolean
	 */
	public function addToSession($asset_id) {
            if(!$this->getGroup()->hasAsset($asset_id)) {
                DBO_Asset_Group_Cnx::assignAssetToGroup($asset_id, $this->getGroup()->id);
            }

            return $this->getGroup()->id;
	}
	
	public function removeFromSession($asset_id) {
            if($this->getGroup()->hasAsset($asset_id)) {
                DBO_Asset_Group_Cnx::removeAssetFromGroup($asset_id, $this->getGroup()->id);
            }

            return $this->getGroup()->id;
	}
	
	/**
	 * Clear out the session variable that holds all
	 * active asset ID's readied for edit
	 */
	public function clear() {
		DBO_Asset_Group_Cnx::removeAllAssets($this->getGroup()->id);
		
		DBO_Group::remove($this->getGroup()->id);
		
		unset($_SESSION[self::SESSION_ASSETS]);
		
		return true;
	}
	
	public function isInEdits($asset_id) {
		if($this->getGroup()->hasAsset($asset_id)) {
			return true;
		}
		
		return false;
	}
	
	public function getEdits() {
		return $this->getGroup()->assets();
	}
	
	public function haveAssets() {
		$assets = $this->getEdits();
		
		if(!empty($assets)) {
			return true;
		}
		
		return false;
	}
}