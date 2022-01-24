<?php
require_once("users.php");

/**
 * @author C.J. Walsh <cj@perigeeglobal.com>
 * @copyright Copyright (c) 2008, C.J. Walsh
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://www.perigeeglobal.com
 * @package Users
 */
class Users_Featured extends Users {
	/**
	 * Constructor
	 *
	 * @return Users_Featured
	 */
	function Users_Featured() {
		$this->Users();
	}
	
	/**
	 * Retrieves all active featured users
	 *
	 * @param string $order MySQL ORDER BY column name
	 * @param string $sort MySQL sort statement
	 * @return array
	 */
	function getAllActive($order = "lastname", $sort = "ASC") {
		global $db;
	
		$sql = "SELECT 
					users.*
				FROM " . DB_TBL_PREFIX . "users_featured AS featured
				LEFT JOIN " . DB_TBL_PREFIX. "users AS users
				ON (users.id = featured.user_id)
				WHERE featured.expires > NOW()
				ORDER BY users.{$order}
				{$sort}";
				
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
	 * Sets a new featured user
	 *
	 * @param string $uid User's ID
	 * @param string $expires Date the featured user's status expires
	 */
	function setFeatured($uid, $expires = null) {
		global $db;
	
		$sql = "INSERT INTO " . DB_TBL_PREFIX . "users_featured (
					user_id, expires
				) VALUES (
					?, ?
				)";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		// don't execute is if user is already featured
		if(!$this->isFeatured($uid)) {
			$res = $db->execute($sth, array(
				$uid,
				(is_null($expires)) ? date("Y-m-d H:i:s", strtotime("+4 weeks")) : date("Y-m-d H:i:s", strtotime($expires))
			));
			if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		}
		
		return;
	}
	
	/**
	 * Unsets a user's featured status
	 *
	 * @param string $uid User's ID
	 */
	function unsetFeatured($uid) {
		global $db;
	
		$sql = "DELETE FROM " . DB_TBL_PREFIX . "users_featured
				WHERE user_id= ?";
				
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		$res = $db->execute($sth, array($uid));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
		
	}
}
?>