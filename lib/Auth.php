<?php
class Auth extends Ode_Auth {
	const SESSION_START = "session_start";
	
	public function __construct() {
		parent::__construct();
	}
	
	public function isAdmin() {
		/**
		 * need to prevent fatal error,
		 * when session object is not available
		 * if it isn't, then we cannot allow admin access.
		 * Same goes for other user types
		 */
		if(!$this->hasSession()) {
			return false;
		}
		
		if($this->getSession()->type()->type_name == DBO_User_Model::ADMIN_TYPE) {
			return true;
		}
		
		return false;
	}
	
	private function setStart() {
		$_SESSION[self::SESSION_START] = time();
	}
	
	private function getStart() {
		return $_SESSION[self::SESSION_START];
	}
	
	public function timeout($redirect_page, $inactivity = 600) {
		$this->setStart();
		
		$sessionLife = time() - $this->getStart();
		
		if($sessionLife > $inactivity) {
			header("Location: " . $redirect_page);
			exit();
		}
		
		return;
	}
	
	public function isEditor() {
		if(!$this->hasSession()) {
			return false;
		}
		
		if($this->getSession()->type()->type_name == DBO_User_Model::EDITOR_TYPE ||
			$this->isAdmin()) {
			return true;
		}
		
		return false;
	}
	
	public function isPhotographer() {
		if(!$this->hasSession()) {
			return false;
		}
		
		if($this->getSession()->type()->type_name == DBO_User_Model::PHOTOG_TYPE ||
			$this->isAdmin()) {
			return true;
		}
		
		return false;
	}
	
	public function isArchivist() {
		if(!$this->hasSession()) {
			return false;
		}
		
		if($this->getSession()->type()->type_name == DBO_User_Model::ARCH_TYPE ||
			$this->isAdmin()) {
			return true;
		}
		
		return false;
	}
	
	public function isGuest() {
		if(!$this->hasSession()) {
			return false;
		}
	
		if($this->getSession()->type()->type_name == DBO_User_Model::GUEST_TYPE) {
			return true;
		}
	
		return false;
	}
	
	public function isManager() {
		if(!$this->hasSession()) {
			return false;
		}
		
		if($this->getSession()->type()->type_name == DBO_User_Model::MANAGER_TYPE || $this->isAdmin()) {
			return true;
		}
		
		return false;
	}
	
	public function isPrivate() {
		if(
			$this->isAdmin() ||
			$this->isArchivist() ||
			$this->isPhotographer() ||
			$this->isEditor() ||
			$this->isManager()
		) {
			return true;
		}
		
		return false;
	}
        
        public function isValidAPI($key, $username) {
            require_once 'phpass-0.3/PasswordHash.php';
            
            $hasher = new PasswordHash(8, false);
            
            $api = DBO_User_API::getOneByUsername($username);
            
            if($hasher->CheckPassword($key.$api->user_id, $api->secret) == true) {
                return true;
            }
            
            return false;
        }
}