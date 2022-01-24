<?php
class DBO_Asset_Model {
	public $id;
	public $public_id;
	public $type_id;
	public $filename;
	public $title;
	public $caption;
	public $description;
	public $photographer_id;
	public $shoot_id;
	public $credit;
	public $location;
	public $lat;
	public $lng;
	public $is_active;
	public $is_deleted;
	public $created;
	public $modified;
	public $modified_by;
	
	public function __construct() {}
	
	public function type() {
		return DBO_Asset_Type::getOneById($this->type_id);
	}
	
	public function isOuttake() {
		$outtake = DBO_Asset_Outtake::getOneByAsset($this->id);
		
		if($outtake != false) {
			return true;
		}
		
		return false;
	}
	
	public function isSelect() {
		$select = DBO_Asset_Select::getOneByAsset($this->id);
		
		if($select != false) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * 
	 * Checks to see if asset is embargoed
	 * If so, return embargo information, else, return false.
	 * @return boolean|DBO_Asset_Embargo_Model
	 */
	public function isEmbargoed() {
		$embargo = DBO_Asset_Restriction_Embargo::getOneByAsset($this->id);
		
		if($embargo != false) {
			return $embargo;
		}
		
		return false;
	}
	
	public function isPublished() {
		$pub = DBO_Asset_Published::getOneByAsset($this->id);
		
		if($pub != false) {
			return true;
		}
		
		return false;
	}
	
	public function isPublic() {
		if($this->isEmbargoed()) return false;
		
		if(!$this->isActive()) return false;
		
		if($this->isExternalRestricted()) return false;
		
		if($this->isHippaRestricted()) return false;
		
		if($this->isInternalRestricted()) return false;
		
		if($this->isNCAARestricted()) return false;
		
		if($this->isSubjectRestricted()) return false;
		
		return true;
	}
	
	public function published() {
		return DBO_Asset_Published::getOneByAsset($this->id);
	}
	
	public function shoot() {
		return DBO_Shoot::getOneById($this->shoot_id);
	}
	
	public function hasShoot() {
		$shoot = $this->shoot();
		
		if($shoot != false) {
			return true;
		}
		
		return false;
	}
	
	public function viewTitle($emptyText = "No title") {
		if(empty($this->title)) {
			return $emptyText;
		}
		
		return $this->title;
	}
	
	public function title($emptyText = "No title") {
		return $this->viewTitle($emptyText);
	}
	
	public function viewDescription($emptyText = "No description") {
		if(empty($this->description)) {
			return $emptyText;
		}
		
		return $this->description;
	}
	
	public function viewCredit($emptyText = "No credit") {
		if(empty($this->credit)) {
			return $emptyText;
		}
		
		return $this->credit;
	}
	
	public function viewCaption($emptyText = "No caption") {
		if(empty($this->caption)) {
			return $emptyText;
		}
		
		return $this->caption;
	}
	
	public function user() {
		return DBO_User::getOneById($this->modified_by);
	}
	
	public function groups() {
		return DBO_Group::getAllByAsset($this->id);
	}
	
	public function keywords() {
		return DBO_Keyword::getAllByAsset($this->id);
	}
	
	public function hasKeywords() {
		$keywords = $this->keywords();
		
		if(!empty($keywords)) {
			return true;
		}
		
		return false;
	}
	
	public function isHippaRestricted() {
		$hippa = DBO_Asset_Restriction_Hippa::getOneByAsset($this->id);
		
		if($hippa != false) {
			return $hippa;
		}
		
		return false;
	}
	
	public function isNCAARestricted() {
		$ncaa = DBO_Asset_Restriction_NCAA::getOneByAsset($this->id);
		
		if($ncaa != false) {
			return $ncaa;
		}
		
		return false;
	}
	
	public function isSubjectRestricted() {
		$subj = DBO_Asset_Restriction_Subject::getOneByAsset($this->id);
		
		if($subj != false) {
			return $subj;
		}
		
		return false;
	}
	
	public function isInternalRestricted() {
		$int = DBO_Asset_Restriction_Internal::getOneByAsset($this->id);
		
		if($int != false) return $int;
		
		return false;
	}
	
	public function isExternalRestricted() {
		$ext = DBO_Asset_Restriction_External::getOneByAsset($this->id);
		
		if($ext != false) return $ext;
		
		return false;
	}
	
	public function photographer() {
		return DBO_Photographer::getOneById($this->photographer_id);
	}
	
	public function finalCaption() {
		return DBO_Caption::getOneByTypeAndAsset($this->id, DBO_Caption_Type::FINAL_NAME);
	}
	
	public function featureCaption() {
		return DBO_Caption::getOneByTypeAndAsset($this->id, DBO_Caption_Type::FEATURE_NAME);
	}
	
	public function filename() {
		return basename($this->filename);
	}
	
	public function genericCaption() {
		return DBO_Caption::getOneByTypeAndAsset($this->id, DBO_Caption_Type::GENERIC_NAME);
	}
	
	public function hasCaption($type) {
		switch($type) {
			case DBO_Caption_Type::FEATURE_NAME:
				if($this->featureCaption() != false) {
					return true;
				}
				break;
			case DBO_Caption_Type::FINAL_NAME:
				if($this->finalCaption() != false) {
					return true;
				}
				break;
			case DBO_Caption_Type::GENERIC_NAME:
				if($this->genericCaption() != false) {
					return true;
				}
				break;
		}
		
		return false;
	}
	
	public function viewKeywords($quote = "") {
		$keywords = $this->keywords();
		
		$ary = array();
		foreach($keywords as $kword) {
			$keyword = $quote . $kword->keyword . $quote;
			
			$ary[] = $keyword;
		}
		
		return implode(", ", $ary);
	}
	
	public function viewFinalCaption() {
		$caption = $this->finalCaption();
		
		if($caption != false) {
			return utf8_decode($caption);
		}
		
		return "";
	}
	
	public function isOrdered() {
		return DBO_Order_LineItem::exists(Order::getOrderId(), $this->id);
	}
	
	public function isFeatured() {
		$feat = Ode_DBO::getInstance()->query("SELECT a.value FROM properties AS a WHERE a.machine_name = 'featured_image' AND a.is_enabled = 1")->fetchColumn();
		
		if($feat) {
			if($this->id == $feat) {
				return true;
			}
		}
		
		return false;
	}
	
	public function copyright() {
		return DBO_Asset_Metadata::get(META_COPYRIGHT_NAME, $this->id);
	}
	
	public function isActive() {
		if($this->is_active == 1) {
			return true;
		}
		
		return false;
	}
	
	public function metadata($field = false, $single = false) {
		if($field == false) {
			return DBO_Asset_Metadata::getAll($this->id);
		} else {
			return DBO_Asset_Metadata::get($field, $this->id, $single);
		}
	}
	
	public function organizations($to_string = false) {
		$meta = self::metadata(DBO_Asset_Metadata::META_ORG_ID_NAME);
		
		$coll = new ArrayObject();
		$str = array();
		foreach($meta as $metum) {
			$org = DBO_Organization::getOneById($metum->metadata_value);
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
	
	/**
	 * Is this asset permitted to be used by supplied user.
	 * @param string $user_id
	 * @return boolean
	 */
	public function allowed($user_id) {
		$user = DBO_User::getOneById($user_id);
		
		/**
		 * allow access to all images for admin types
		 */
		if($user->type()->type_name == DBO_User_Model::ADMIN_TYPE) {
			return true;
		}
		
		$user_orgs = DBO_User_Organization_Cnx::getUserOrgIDs($user_id);
		
		$asset_meta = DBO_Asset_Metadata::get(DBO_Asset_Metadata::META_ORG_ID_NAME, $this->id, true);
		
		if($asset_meta == false) {
			return false;
		}
		
		if(in_array($asset_meta->metadata_value, $user_orgs)) {
			return true;
		}
		
		return false;
	}
	
	public function views($zero_txt = false) {
		$meta = DBO_Asset_Metadata::get(DBO_Asset_Metadata::META_NUM_VIEWS, $this->id, true);
		
		$num = 0;
		$str = "";
		if(!empty($meta)) {
			$num = $meta->metadata_value;
		}
		
		if($num <= 0) {
			$str .= ($zero_txt == false) ? $num : $zero_txt;
		} else {
			$str .= number_format(floatval($num));
		}
		
		if($num == 1) {
			$str .= " view";
		} else {
			$str .= " views";
		}
		
		return $str;
	}
	
	public function rights($get_data = false) {
		$rights = DBO_Asset_Metadata::get(DBO_Asset_Metadata::META_RIGHTS, $this->id);
		
		if($get_data == true) {
			return $rights;
		}
		
		if(!empty($rights)) {
			if(empty($rights->metadata_value)) {
				return "No rights";
			}
			
			$prop = DBO_Properties::getOneById($rights->metadata_value);
			return $prop->value;
		}
		
		return "No rights";
	}
}