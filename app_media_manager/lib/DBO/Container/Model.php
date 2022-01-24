<?php
class DBO_Container_Model {
	public $id;
	public $title;
	public $description;
	public $is_approved;
	public $is_deleted;
	public $created;
	public $modified;
	
	public function title($text = "No title") {
		return (empty($this->title)) ? $text : $this->title;
	}
	
	public function groups() {
		return DBO_Container::getGroups($this->id);
	}
	
	/**
	 * Return all publicly avaialable sets/groups for
	 * this container/collection
	 * @return DBO_Group_Model[]
	 */
	public function publicGroups($limit = false) {
		return DBO_Group::getAllPublicByContainer($this->id, $limit);
	}
	
	public function organizations($to_string = false) {
		$meta = self::metadata(DBO_Container_Metadata::META_ORG_ID_NAME);
		
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
		
		return $coll->getArrayCopy();
	}
	
	public function metadata($meta_key = false, $single = false) {
		if($meta_key == false) {
			return DBO_Container_Metadata::getAll($this->id);
		} else {
			return DBO_Container_Metadata::get($meta_key, $this->id, $single);
		}
	}
	
	public function assets() {
		return DBO_Asset::getAllByContainer($this->id);
	}
	
	public function publicAssets() {
		return DBO_Asset::getAllPublicByContainer($this->id);
	}
	
	public function viewIsApproved($default = array("Yes", "No")) {
		return ($this->is_approved == 1) ? $default[0] : $default[1];
	}
	
	public function description($text = "No description") {
		return (empty($this->description)) ? $text : $this->description;
	}
}
?>