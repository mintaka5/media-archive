<?php
class DBO_Publication_Model {
	public $id;
	public $title;
	public $created;
	public $modified;
	public $modified_by;
	
	public function __construct() {}
	
	public function __toString() {
		return $this->title;
	}
}