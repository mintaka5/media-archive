<?php
class DBO_User_Organization_Cnx_Model {
	public $id;
	public $user_id;
	public $org_id;
	
	public function user() {
		return DBO_User::getOneById($this->user_id);
	}
	
	public function organization() {
		return DBO_Organization::getOneById($this->org_id);
	}
}