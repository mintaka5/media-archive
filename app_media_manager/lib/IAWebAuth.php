<?php
class IAWebAuth extends WebAuth {
	public function __construct() {
		parent::WebAuth();
	}
	
	/**
	 * Check whether or not the user from WebAuth is
	 * registered with the image archive
	 * 
	 * @param UCI_LDAP $ldap
	 * @access public
	 * @return mixed:DBO_User|boolean
	 */
	public function checkRegistration(UCI_LDAP $ldap) {
		$user = DBO_User_WebAuth::getOneByCampusId($ldap->getCampusId());	
		
		/**
		 * Grab organization
		 * If doesn't exist, create in DB
		 */
		$org = DBO_Organization::getOneByName($ldap->getDepartmentNum());
		$org_id = $org->id;
		if($org == false) {
			$org_id = DBO_Organization::add($ldap->getDepartmentNum(), $ldap->getDepartment());
		}
		
		$regUser = false;
		
		if($user != false) {
			/**
			 * user does not belong to LDAP department in DB so add them and reset user object
			 */
			if(!DBO_User_Organization_Cnx::isInOrganization($user->user_id, $org_id)) {
				DBO_User_Organization_Cnx::addUserToOrg($user->user_id, $org_id);
					
				$user = DBO_User_WebAuth::getOneByCampusId($ldap->getCampusId());
			}
			
			$regUser = $user->user();
		} else {
			$user = DBO_User::getOneByUsername($ldap->getUCINetId());
			
			$userId = $this->register($ldap);
				
			$regUser = DBO_User::getOneById($userId);
		}
		
		return $regUser;
	}
	
	/**
	 * register new users coming from WebAuth
	 * 
	 * @param UCI_LDAP $ldap
	 * @access private
	 * @return mixed:boolean|string User ID
	 */
	private function register(UCI_LDAP $ldap) {
		$user = new stdClass();
		$user->username = $ldap->getUCINetId();
		$user->password = "emptypassword";
		$user->email = $ldap->getEmail();
		
		$fullnameAry = explode(",", $ldap->getLastFirstName());
		$user->firstname = trim($fullnameAry[1]);
		$user->lastname = trim($fullnameAry[0]);
		
		$_user = DBO_User::getOneByUsername($ldap->getUCINetId());
		
		/**
		 * Only create user if they do not exist as a guest
		 */
		if($_user == false) {
			$userId = DBO_User::registerGuest($user);
		} else {
			$userId = $_user->id;
		}
		
		/**
		 * @todo find a way to query user types easily
		 *
		 * Set new registered user to type GUEST
		 */
		DBO_User_Type_Cnx::update($userId, 5);
		
		/**
		 * Put the campus ID into the webauth table
		 */
		DBO_User_WebAuth::add($ldap->getCampusId(), $userId);
		
		return $userId;
	}
}