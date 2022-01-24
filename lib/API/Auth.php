<?php
class API_Auth implements iAuthenticate {
	const API_KEY_NAME = "api_key";
	const USERNAME_NAME = "user";
	
	
	public function __isAuthenticated() {
		$apiKey = trim($_GET[self::API_KEY_NAME]);
		$username = trim($_GET[self::USERNAME_NAME]);
				
		if(Ode_Auth::getInstance()->isValidAPI($apiKey, $username)) {
			return true;
		}
		
		return false;
	}
	
}
?>