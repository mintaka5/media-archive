<?php
require_once('UUID.php');

@define('DPI_COOKIE_EXPIRES', time() + 86400);
@define('DPI_COOKIE_ID_NAME', 'dpi_guid');

class Users_Lock {
	function Users_Lock() {
		if(!isset($_COOKIE[DPI_COOKIE_ID_NAME])) {
			setcookie(DPI_COOKIE_ID_NAME, UUID::get(), DPI_COOKIE_EXPIRES);
		}
	}
	
	function isValidAccount($user_id) {
		$user = $this->userExists($user_id);
		
		if(!$user) {
			if(!$this->idExists($_COOKIE[DPI_COOKIE_ID_NAME])) {
				$this->insert($_COOKIE[DPI_COOKIE_ID_NAME], $user_id, $_SERVER['REMOTE_ADDR']);
			} else { // avpid collisions of different users on same computer
				$new_id = UUID::get();
				
				setcookie(DPI_COOKIE_ID_NAME, $new_id, DPI_COOKIE_EXPIRES);
				
				$this->insert($new_id, $user_id, $_SERVER['REMOTE_ADDR']);
			}
			
			return true;
		} else {
			if($user['id'] != $_COOKIE[DPI_COOKIE_ID_NAME]) {
				if($user['ip_address'] == $_SERVER['REMOTE_ADDR']) {
					setcookie(DPI_COOKIE_ID_NAME, $user['id'], DPI_COOKIE_EXPIRES);
					
					return true;
				} else {
					return false;
				}
			} else {
				if($user['ip_address'] != $_SERVER['REMOTE_ADDR']) {
					return false;
				} else {
					return true;
				}
			}
		}
	}
	
	function idExists($cookie_id) {
		global $db;
		
		$sql = "SELECT id
				FROM " . DB_TBL_PREFIX . "user_lock
				WHERE id = '{$cookie_id}'";
		
		$res = $db->getOne($sql);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return (is_null($res)) ? false : true;
	}
	
	function insert($cookie_id, $user_id, $ip_address) {
		global $db;
		
		$sql = "INSERT INTO " . DB_TBL_PREFIX . "user_lock (
					id, user_id, ip_address, created
				) VALUES (
					?, ?, ?, NOW()
				)";
		
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		$res = $db->execute($sth, array($cookie_id, $user_id, $ip_address));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	function userExists($user_id) {
		global $db;
		
		$sql = "SELECT *
				FROM " . DB_TBL_PREFIX . "user_lock
				WHERE user_id = '{$user_id}'";
		
		$res = $db->getRow($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return (empty($res)) ? false : $res;
	}
}
?>