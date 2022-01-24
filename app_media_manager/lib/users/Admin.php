<?php
require_once("users.php");

/**
 * @author C.J. Walsh <cj@perigeeglobal.com>
 * @copyright Copyright (c) 2008, C.J. Walsh
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://www.perigeeglobal.com
 * @package Users
 * 
 */
class Users_Admin extends Users {
	/**
	 * Constructor
	 *
	 * @return Users_Admin
	 */
	function Users_Admin() {
		$this->Users();
	}

	/**
	 * Sets a user to approved status
	 *
	 * @param string User's ID
	 */
	function approveUser($uid) {
		global $db;
		
		$sql = "UPDATE ". DB_TBL_PREFIX . "users
				SET
					approved = ?
				WHERE id = ?";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		$res = $db->execute($sth, array(1, $uid));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Retrieve a limit of users
	 * who've just recently registered.
	 *
	 * @param string $order MySQL ORDER BY column name
	 * @param string $sort MySQL sort statement
	 * @param integer $limit Limits number of results
	 * @return array
	 */
	function getRecentJoined($order = "lastname", $sort = "ASC", $limit = 10) {
		global $db;
		
		$sql = "SELECT *
				FROM " . DB_TBL_PREFIX . "users
				WHERE approved <= 0
				AND deleted = 0
				AND password != ''
				AND created >= '" . date("Y-m-d H:i:s", strtotime("-1 week")) . "'
				ORDER BY {$order}
				{$sort}
				LIMIT 0,{$limit}";
		
		//echo $sql;
		
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		$ary = array();
		if(!empty($res)) {
			foreach($res as $k => $v) {
				$ary[] = $this->setUser($v);
			}
		}
		
		return $ary;
	}
	
	/**
	 * Sets the user status level (the 'approved' column)
	 *
	 * 0: Registered, but not approved
	 * 1: Pro user
	 * 100: Admin
	 * 500: Super admin
	 *
	 * @param string $id User's ID
	 * @param integer $lvl Access level number
	 */
	function setStatus($id, $lvl) {
		global $db;
		
		$sql = "UPDATE " . DB_TBL_PREFIX . "users
				SET
					approved = ?
				WHERE id = ?";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		$res = $db->execute($sth, array($lvl, $id));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Delete a user. Also used to ban
	 * or disable account from site.
	 *
	 * @param string $id User's ID
	 */
	function delete($id) {
		global $db;
	
		$sql = "UPDATE " . DB_TBL_PREFIX . "users
				SET deleted = ?
				WHERE id = ?";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		$res = $db->execute($sth, array(1, $id));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Reactivate user account that's
	 * been deleted. Also reverses a banned user
	 *
	 * @param string $id User's ID
	 */
	 function undelete($id) {
	 	global $db;
	 	
	 	$sql = "UPDATE " . DB_TBL_PREFIX . "users
	 			SET deleted = ?
	 			WHERE id = ?";
	 	$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		$res = $db->execute($sth, array(0, $id));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	 }
}
?>