<?php
class DBO_User_WebAuth_Model {
	public $id;
	public $campusid;
	public $user_id;
	public $created;
	
	public function user() {
		return DBO_User::getOneById($this->user_id);
	}
}
?>