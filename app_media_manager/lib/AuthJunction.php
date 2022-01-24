<?php
class AuthJunction {
	/**
	 * WebAuth object instance
	 * @var IAWebAuth
	 */
	private $webauth = false;
	
	/**
	 * Default Auth object instance
	 * @var Auth
	 */
	private $default = false;
	
	/**
	 * Login URL needed for WebAuth, but also 
	 * @var unknown_type
	 */
	private $loginUrl = null;
	private $type = 0;
	
	public function __construct($authType = 0) {
		$this->setLoginUrl(BASE_URL . "?_page=auth");
		$this->type = $authType;
		
		if($authType == 0) {
			$this->default = new Auth();
		} else {
			$this->webauth = new IAWebAuth();
		}
	}
	
	public function setLoginUrl($url) {
		$this->loginUrl = $url;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getLoginUrl() {
		return $this->loginUrl;
	}
	
	/**
	 * @return boolean
	 */
	public function isLoggedIn() {
		if($this->type = 0) {
			return $this->getWebAuth()->isLoggedIn();
		} else {
			return $this->getDefaultAuth()->isAuth();
		}
	}
	
	/**
	 * @return IAWebAuth|boolean
	 */
	private function getWebAuth() {
		if($this->webauth != false) {
			return $this->webauth;
		}
		
		return false;
	}
	
	/**
	 * @return Auth|boolean
	 */
	private function getDefaultAuth() {
		if($this->default != false) {
			return $this->default;
		}
		
		return false;
	}
}
?>