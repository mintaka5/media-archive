<?php
class DBO_Shoot_Model {
	public $id;
	public $shoot_name;
	public $title;
	public $shoot_date;
	public $description;
	public $is_active;
	public $created;
	public $modified;
	public $modified_by;
	
	public function user() {
		return DBO_User::getOneById($this->modified_by);
	}
}
?>