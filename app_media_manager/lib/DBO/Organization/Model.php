<?php
class DBO_Organization_Model {
	public $id;
	public $org_name;
	public $title;
	public $is_deleted;
	public $created;
	public $created_by;
	
	public function user() {
		return DBO_User::getOneById($this->created_by);
	}
	
	public function users() {
		return DBO_User_Organization_Cnx::getAllUsersByOrg($this->id);
	}
	
	public function numUsers() {
		$users = $this->users();
		
		return count($users);
	}
        
        public function title($notitle = 'Untitled') {
            if(!empty($this->title)) {
                return stripslashes($this->title);
            }
            
            return $notitle;
        }
        
        public function metadata($meta_name = false, $single = false) {
            if($meta_name == false) {
                return DBO_Organization_Metadata::getAll($this->id);
            } else {
                return DBO_Organization_Metadata::get($meta_name, $this->id, $single);
            }
	}
}
?>