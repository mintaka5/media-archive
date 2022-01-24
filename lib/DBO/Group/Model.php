<?php
class DBO_Group_Model {
	public $id;
	public $title;
	public $date_start;
	public $date_end;
	public $is_approved;
	public $is_deleted;
	public $created;
	public $modified;
	public $modified_by;
	
	/**
	 * 
	 * @param string $text Default text to display if no title is specified.
	 * @return string
	 */
	public function title($text = "No title") {
		return (empty($this->title)) ? $text : $this->title;
	}
	
	/**
	 * 
	 * Text to be used for group approval status
	 * @param array $default Index 0: is_approved = 1; Index 1: is_approved = 0
	 */
	public function viewIsApproved($default = array("Yes", "No")) {
		return ($this->is_approved == 0) ? $default[1] : $default[0];
	}
	
	/**
	 * 
	 * @return mixed boolean|DBO_User_Model
	 */
	public function user() {
		return DBO_User::getOneById($this->modified_by);
	}
	
	/*public function containers() {
		return DBO_Container::getAllByGroup($this->id);
	}*/
	
	/**
	 * Retrieves the default asset for a group. If
	 * one does not exist yet, then provide a random one,
	 * and if none exist return false
	 * @return mixed boolean|DBO_Asset_Model
	 */
	public function defaultAsset() {
		$selected = DBO_Group::getDefaultAsset($this->id);
		
		if($selected != false) {
			return $selected;
		} else {
			$assets = $this->assets();
			
			if(!empty($assets)) {
				$key = array_rand($assets, 1);
				$asset = $assets[$key];
			
				return $asset;
			}
		}
		
		return false;
	}
	
	/**
	 * Gets all assets belonging to a group
	 * @return mixed boolean|DBO_Asset_Model
	 */
	public function assets() {
		return DBO_Asset::getAllByGroup($this->id);
	}
        
        /**
         * Grab only an array of IDs for all of this group's assets
         * @return array Asset IDs
         */
        public function assetIds() {
            $assets = $this->assets();
            $ids = array();
            foreach ($assets as $asset) {
                $ids[] = $asset->id;
            }
            
            return $ids;
        }
	
	/**
	 * Returns the number of active assets belonging to a group (for front end/public users only
	 * @return integer
	 */
	public function numAssets() {
		return DBO_Asset::getNumActiveByGroup($this->id);
	}
	
	public function numTotalAssets() {
		return DBO_Asset::getNumTotalByGroup($this->id);
	}
	
	/**
	 * Each archive will have an image that will be featured on the home page.
	 * @return boolean
	 */
	public function isFeatured() {
		$prop = Ode_DBO::getInstance()->query("
			SELECT " . DBO_Properties::COLUMNS . "
			FROM " . DBO_Properties::TABLE_NAME . " AS a
			WHERE a.is_enabled = 1
			AND a.machine_name = '" . DBO_Properties::FEATURED_GROUPS . "'
			LIMIT 0,1
		")->fetchObject();
		
		$json = new Services_JSON();
		$groupIds = $json->decode($prop->value);
		
		if(in_array($this->id, $groupIds)) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Checks to see whether or not a group has any active/approved assets in it.
	 * @return boolean
	 */
	public function hasApprovedAssets() {
		$assets = DBO_Asset::getActiveByGroup($this->id);
		
		if(!empty($assets)) {
			return true;
		}
		
		return false;
	}
	
	public function hasAsset($asset_id) {
		$assets = $this->assets();
		
		foreach($assets as $asset) {
			if($asset->id == $asset_id) {
				return true;
			}
		}
		
		return false;
	}
	
	public function organizations($to_string = false) {
            $meta = self::metadata(DBO_Group_Metadata::META_ORG_ID_NAME);

            $coll = new ArrayObject();
            $str = array();

            foreach($meta as $metum) {
                $org = DBO_Organization::getOneById($metum->meta_value);
                $str[] = $org->title;
                $coll->append($org);
            }

            if($to_string != false) {
                if(!empty($str)) {
                        return implode(", ", $str);
                } else {
                        return $to_string;
                }
            }

            return $coll;
	}
	
	public function metadata($meta_name = false, $single = false) {
            if($meta_name == false) {
                return DBO_Group_Metadata::getAll($this->id);
            } else {
                return DBO_Group_Metadata::get($meta_name, $this->id, $single);
            }
	}
        
        public function isBatch() {
            $meta = $this->metadata(DBO_Group_Metadata::META_IS_BATCH_GROUP, true);
            
            if($meta->meta_value == 1) {
                return true;
            }
            
            return false;
        }
}