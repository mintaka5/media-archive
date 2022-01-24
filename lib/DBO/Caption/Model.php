<?php
class DBO_Caption_Model {
	public $id;
	public $type_id;
	public $asset_id;
	public $caption;
	public $modified_by;
	public $is_active;
	public $created;
	public $modified;
	
	public function __construct() {
		//Util::debug($this);
	}
	
	public function __toString() {
		if($this != false) {
			return $this->caption;
		}
		
		return "No caption";
	}
}
?>