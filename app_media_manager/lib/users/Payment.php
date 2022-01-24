<?php
/**
 * @author C.J. Walsh <cj@perigeeglobal.com>
 * @copyright Copyright (c) 2008, C.J. Walsh
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://www.perigeeglobal.com
 * @package Users
 * 
 */
class Users_Payment {
	/**
	 * SELECT statement constructor
	 *
	 * @var string
	 */
	var $selectStatement;

	/**
	 * FROM statement constructor
	 *
	 * @var string
	 */
	var $tablesStatment;

	/**
	 * Constructor
	 *
	 * @return Users_Payment
	 */
	function Users_Payment() {
		$this->tablesStatement = DB_TBL_PREFIX . "user_payment_info AS payment
								LEFT JOIN " . DB_TBL_PREFIX . "users AS users ON (users.id = payment.user_id)
								LEFT JOIN " . DB_TBL_PREFIX . "user_billing AS billing ON (billing.user_id = payment.user_id)";
								
		$this->selectStatement = "payment.*,
									CONCAT(users.firstname, ' ', users.lastname) AS user_fullname,
									users.email AS user_email,
									CONCAT(billing.address_one, ' ', billing.address_two) AS billing_address,
									billing.city AS billing_city,
									billing.state AS billing_state,
									billing.zip AS billing_zip,
									billing.phone AS billing_phone";
	}
	
	/**
	 * Get FROM statement construction
	 *
	 * @return string
	 */
	function getTablesStatement() {
		return $this->tablesStatement;
	}
	
	/**
	 * Get SELECT statement construction
	 *
	 * @return string
	 */
	function getSelectStatement() {
		return $this->selectStatement;
	}
	
	/**
	 * Retrieve user's payment information.
	 *
	 * @param string $user_id
	 * @return array
	 */
	function getOneByUser($user_id) {
		global $db;
		
		$sql = "SELECT " . $this->getSelectStatement() . "
				FROM " . $this->getTablesStatement() . "
				WHERE payment.user_id = '{$user_id}'";
		
		$res = $db->getRow($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Checks to see if user has payment
	 * information avaialable.
	 *
	 * @param string $user_id
	 * @return boolean
	 */
	function userHasInfo($user_id) {
		global $db;
	
		$sql = "SELECT id
				FROM " . DB_TBL_PREFIX . "user_payment_info
				WHERE user_id = '{$user_id}'";
		$res = $db->getOne($sql);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return (is_null($res)) ? false : $res;
	}
	
	/**
	 * Insert new user's payment information.
	 *
	 * @param string $user_id
	 * @param integer $type_id
	 * @param string $enc_num
	 * @param string $expiration
	 */
	function insert($user_id, $type_id, $enc_num, $expiration) {
		global $db;
		
		$sql = "INSERT INTO " . DB_TBL_PREFIX . "user_payment_info (
					id, user_id, type_id,
					ccnum, expires, created,
					modified
				) VALUES (
					UUID(), ?, ?,
					?, ?, NOW(),
					NOW()
				)";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		$res = $db->execute($sth, array(
			$user_id, $type_id,
			$enc_num, $expiration
		));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Update user's payment information.
	 *
	 * @param string $id
	 * @param integer $type_id
	 * @param string $enc_num
	 * @param string $expiration
	 */
	function update($id, $type_id, $enc_num, $expiration) {
		global $db;
		
		$sql = "UPDATE " . DB_TBL_PREFIX . "user_payment_info
				SET
					type_id = ?,
					ccnum = ?,
					expires = ?,
					modified = NOW()
				WHERE id = ?";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		$res = $db->execute($sth, array(
			$type_id, $enc_num,
			$expiration, $id
		));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
}
?>