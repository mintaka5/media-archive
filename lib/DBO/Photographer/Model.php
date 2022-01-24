<?php
class DBO_Photographer_Model {
	public $id;
	public $firstname;
	public $lastname;
	public $modified_by;
	
	public function __construct() {}
	
	public function fullname($lastFirst = false) {
		if($lastFirst == true) {
			return $this->lastname . ", " . $this->firstname;
		} else {
			return $this->firstname . " " . $this->lastname;
		}
	}
	
	public function __toString() {
		return $this->fullname();
	}
}