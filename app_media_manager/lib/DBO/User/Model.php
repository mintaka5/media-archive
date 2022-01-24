<?php
class DBO_User_Model {
	public $id;
	public $username;
	public $email;
	public $firstname;
	public $lastname;
	public $is_active;
	public $is_deleted;
	public $created;
	public $modified;
	
	const ADMIN_TYPE = "admin";
	const EDITOR_TYPE = "editor";
	const PHOTOG_TYPE = "photo";
	const ARCH_TYPE = "archive";
	const GUEST_TYPE = "guest";
	const MANAGER_TYPE = "manage";
	
	public function __construct() {}
	
	public function type() {
		return DBO_User_Type::getOneByUser($this->id);
	}
	
	public function fullname($lastFirst = false) {
		if($lastFirst == true) {
			return $this->lastname . ", " . $this->firstname;
		} else {
			return $this->firstname . " " . $this->lastname;
		}
	}
        
    public function api() {
    	return DBO_User_API::getOneByUser($this->id);
    }
	
    public function organizations() {
	return DBO_User_Organization_Cnx::getAllByUser($this->id);
    }
    
    public function isSelf($session_id) {
        if($session_id == $this->id) {
            return true;
        }
        
        return false;
    }
}
?>