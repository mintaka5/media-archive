<?php
require_once("users/Payment.php");

/**
 * @author C.J. Walsh <cj@perigeeglobal.com>
 * @copyright Copyright (c) 2008, C.J. Walsh
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://www.perigeeglobal.com
 * @package Users
 * 
 */
class Users_Billing {
	/**
	 * Users Payment instance
	 *
	 * @var object
	 */
	var $payment;

	/**
	 * Constructor
	 *
	 * @return Users_Billing
	 */
	function Users_Billing() {
		$this->payment = new Users_Payment();
	}
	
	/**
	 * Retrieve a user's billing information via user's ID
	 *
	 * @param string User's ID
	 * @return array
	 */
	function getOneByUserId($id) {
		global $db;
		
		$sql = "SELECT * FROM ".DB_TBL_PREFIX."user_billing
			WHERE user_id = '{$id}'";
		
		$res = $db->getRow($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Retrieve a user's billing information via billing ID
	 *
	 * @param string Billing ID
	 * @return array
	 */
	function getOneById($id) {
		global $db;
		
		$sql = "SELECT * FROM ".DB_TBL_PREFIX."user_billing
			WHERE id = '{$id}'";
		
		$res = $db->getRow($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Insert user's billing information
	 *
	 * @param string $uid User's ID
	 * @param string $addr1 Address line one
	 * @param string $addr2 Address line two
	 * @param string $city
	 * @param string $state
	 * @param integer $country_id
	 * @param string $zip
	 * @param string $phone
	 */
	function insert($uid, $addr1, $addr2, $city, $state, $country_id, $zip, $phone = null) {
		global $db;
		
		$sql = "INSERT INTO ".DB_TBL_PREFIX."user_billing (
				user_id, address_one, address_two,
				city, state, country_id, zip, phone, created, modified
			) VALUES (
				?, ?, ?,
				?, ?, ?, ?, ?, NOW(), NOW()
			)";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		$res = $db->execute($sth, array(
			$uid, trim($addr1), trim($addr2),
			trim($city), $state, $country_id, trim($zip), 
			(!is_null($phone)) ? Misc::formatPhone(trim($phone)) : $phone
		));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Update user's billing information
	 *
	 * @param string $id Billing ID
	 * @param string $addr1 Address line one
	 * @param string $addr2 Address line two
	 * @param string $city
	 * @param string $state
	 * @param string $zip
	 * @param string $phone
	 */
	function update($id, $addr1, $addr2, $city, $state, $zip, $phone = null) {
		global $db;
		
		$sql = "UPDATE ".DB_TBL_PREFIX."user_billing
			SET
				address_one = ?,
				address_two = ?,
				city = ?,
				state = ?,
				zip = ?,
				phone = ?,
				modified = NOW()
			WHERE user_id = ?";
		
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		$res = $db->execute($sth, array(
			trim($addr1), trim($addr2),
			trim($city), $state,
			trim($zip), 
			(!is_null($phone)) ? Misc::formatPhone(trim($phone)) : '', 
			$id
		));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Insert a new user's email and username
	 *
	 * @param string $email Email address
	 * @param string $uname Username
	 */
	function insertUsername($email, $uname) {
		global $db;
		
		$sql = "INSERT INTO " . DB_TBL_PREFIX . "users (
					username, email
				) VALUES (
					?, ?
				)";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		$res = $db->execute($sth, array(trim($email), trim($uname)));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $this->getIdByEmail($email);
	}
}