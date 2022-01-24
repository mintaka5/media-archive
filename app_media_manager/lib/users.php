<?php
require_once("users/Billing.php");
require_once("users/Biography.php");

/**
 * @author C.J. Walsh <cj@perigeeglobal.com>
 * @copyright Copyright (c) 2008, C.J. Walsh
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://www.perigeeglobal.com
 * @package Users
 * 
 */
class Users {
	/**
	 * User_Billing class
	 *
	 * @var object
	 */
	var $billing;
	
	/**
	 * User status levels
	 *
	 * @var array
	 */
	var $stati = array();
	
	/**
	 * Users Biography instance
	 *
	 * @var object
	 */
	var $bio;
	
	/**
	 * Constructor
	 *
	 * @return Users
	 */
	function Users() {
		$this->billing = new Users_Billing();
		
		$this->bio = new Users_Biography();
		
		$this->stati = array(
			0 => "Registered",
			1 => "Pro",
			100 => "Admin",
			500 => "Super admin"
		);
	}

	/**
	 * Set up all user data and related information
	 *
	 * @param array $data
	 * @return array
	 */
	function setUser($data) {
		$ary = array();
		
		if(!empty($data)) {
			foreach($data as $k => $v) {
				$ary[$k] = $v;
			}
			
			$ary['status'] = $this->getStatus($data['approved']);
			$ary['billing'] = $this->billing->getOneByUserId($data['id']);
			$ary['photo'] = $this->getProfileImage($data['id']);
			$ary['featured'] = $this->isFeatured($data['id']);
			
			$ary['bio'] = $this->bio->getOneByUser($data['id']);
			
			// what if user is deleted?
			if($data['deleted'] == 1) {
				$ary['status'] = $this->getStatus();
			}
		}
		
		return $ary;
	}
	
	/**
	 * Check to see if a user is featured.
	 *
	 * @param string $id
	 * @return boolean
	 */
	function isFeatured($id) {
		global $db;
		
		$sql = "SELECT id
				FROM " . DB_TBL_PREFIX . "users_featured
				WHERE user_id = '{$id}'";
		$res = $db->getOne($sql);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return (is_null($res)) ? false : $res;
	}
	
	/**
	 * Insert new user portrait image.
	 *
	 * @param string $imgid
	 * @param string $uid
	 * @return integer
	 */
	function insertProfileImage($imgid, $uid) {
		global $db;
		
		// remove unique portrait image for
		// user before inserting new one
		$this->removeProfileImageByUser($uid);
		
		$cnxid = uniqid("", true);
		
		$sql = "INSERT INTO " . DB_TBL_PREFIX . "user_images_cnx (
					id, user_id, image_id
				) VALUES (
					?, ?, ?
				)";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		$res = $db->execute($sth, array($cnxid, $uid, $imgid));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $cnxid;
	}
	
	function removeProfileImageByUser($user_id) {
		global $db;
		
		$image_record = $this->getProfileImage($user_id);
		
		$sql = "DELETE FROM " . DB_TBL_PREFIX . "user_images_cnx WHERE user_id = ?";
		
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		$res = $db->execute($sth, array($user_id));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		require_once('Gallery/Images.php');
		$imgobj = new Gallery_Images();
		$imgobj->delete($image_record['image_id']);
		
		return;
	}
	
	/**
	 * Retrieve user's profile portrait
	 *
	 * @param string $uid
	 * @return array
	 */
	function getProfileImage($uid) {
		global $db;
		
		$sql = "SELECT 
					cnx.*
				FROM " . DB_TBL_PREFIX . "user_images_cnx AS cnx
				LEFT JOIN " . DB_TBL_PREFIX . "gallery_images AS images
				ON (images.id = cnx.image_id)
				WHERE cnx.user_id = '{$uid}'
				AND images.deleted = 0
				LIMIT 0,1";
		$res = $db->getRow($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR); 
		
		return is_null($res) ? false : $res;
	}
	
	/**
	 * Check to see if user email already exists
	 *
	 * @param string $email
	 * @return boolean
	 */
	function emailExists($email) {
		global $db;
		
		$sql = "SELECT email FROM ".DB_TBL_PREFIX."users
			WHERE email = '{$email}'
			AND deleted = 0";
		$res = $db->getOne($sql);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR); 
		
		return !is_null($res) ? true : false;
	}	
	
	/**
	 * Retrieve user by ID
	 *
	 * @param string $id
	 * @return array
	 */
	function getById($id) {
		global $db;
		
		$sql = "SELECT users.*, CONCAT(users.firstname, ' ', users.lastname) AS ufullname
				FROM ".DB_TBL_PREFIX."users AS users WHERE id = '{$id}'";
				
		$res = $db->getRow($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) {
			trigger_error($res->getDebugInfo(), E_USER_ERROR);
		}
		
		return $this->setUser($res);
	}
	
	/**
	 * Insert new user into database
	 *
	 * @param string $email
	 * @param string $passwd
	 * @param string $fname
	 * @param string $lname
	 * @return boolean
	 */
	function insert($email, $passwd, $fname, $lname) {
		global $db;
		
		$sql = "INSERT INTO ". DB_TBL_PREFIX . "users (
				id,
				email, password, firstname,
				lastname, directory, created
			) VALUES (
				?,
				?, MD5(?), ?,
				?, ?, NOW()
			)";
		
		$bool = true;
		
		$sth = $db->prepare($sql);
		if(DB::isError($sth))  $bool = false;
		$res = $db->execute($sth, array(
			uniqid("", true),
			$email, $passwd, $fname,
			$lname, uniqid(strtolower($lname))
		));
		if(DB::isError($res)) {
			$bool = false;
		} else {
			$bool = $this->getIdByEmail($email);
			$this->createDir($bool);
		}
		
		return $bool;
	}
	
	/**
	 * Retrieve user ID by email address
	 *
	 * @param string $email
	 * @return boolean ID if true
	 */
	function getIdByEmail($email) {
		global $db;
		
		$sql = "SELECT id FROM ".DB_TBL_PREFIX."users
			WHERE email = '{$email}'
			AND deleted = 0";
		$res = $db->getOne($sql);
		
		if(!is_null($res)) {
			return $res;
		} else {
			return false;
		}
	}
	
	/**
	 * Retrieve all users
	 *
	 * @param string $order
	 * @param string $sort
	 * @return array
	 */
	function getAll($order = "lastname", $sort = "ASC", $limit = null) {
		global $db;
		
		$sql = "SELECT *
				FROM " . DB_TBL_PREFIX . "users
				WHERE password != ''
				ORDER BY {$order}
				{$sort}";
		
		//echo $sql;
		
		if(!is_null($limit)) {
			$sql .= " LIMIT 0,{$limit}";
		}
		
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
	 * Retrieve users by searching across
	 * user first name, last name, or email.
	 * Returns only users who are not in delete status.
	 *
	 * @param string $str Search string
	 * @param string $type
	 * @param string $order
	 * @param string $sort
	 * @return array
	 */
	function search($str, $type = null, $order = "lastname", $sort = "ASC") {
		global $db;
		
		$qry = preg_replace("#[\W\s]+#", "%", $str);
	
		$sql = "SELECT * FROM ".DB_TBL_PREFIX."users
				WHERE ";
				
		if(is_null($type)) {
			$sql .= "firstname LIKE '%{$qry}%'
					OR lastname LIKE '%{$qry}%'
					OR email LIKE '%{$qry}%' AND ";
		} else if($type == "lastname") {
			$sql .= "lastname LIKE '%{$qry}%' AND ";
		} else if($type == "email") {
			$sql .= "email LIKE '%{$qry}%' AND ";
		}
		
		$sql .= " approved < 500
				AND deleted = 0
				AND password != ''
				ORDER BY {$order}
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
	 * Insert username and email
	 * for newly registered users
	 *
	 * @param string $email
	 * @param string $username
	 * @return string User ID
	 */
	function insertUsername($email, $username) {
		global $db; 
		
		$uniqid = preg_replace("#[\W]+#", "", uniqid("", true));
		
		$sql = "INSERT INTO " . DB_TBL_PREFIX . "users (
					id,
					username, email,
					created
				) VALUES (
					?, ?, ?, NOW()
				)";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		$res = $db->execute($sth, array($uniqid, trim($username), trim($email)));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $uniqid;
	}
	
	/**
	 * Update newly registered users
	 *
	 * @param string $id
	 * @param string $fname
	 * @param string $lname
	 * @param string $passwd
	 */
	function updateNewUser($id, $fname, $lname, $jobt, $company, $bus, $passwd) {
		global $db;
		
		$sql = "UPDATE " . DB_TBL_PREFIX . "users
				SET
					firstname = ?,
					lastname = ?,
					job_type_id = ?,
					company = ?,
					bus_type_id = ?,
					password = MD5(?)
				WHERE id = ?";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		$res = $db->execute($sth, array(
			trim($fname), 
			trim($lname), 
			$jobt,
			trim($company),
			$bus,
			trim($passwd), 
			$id
		));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Retrieve email by user ID
	 *
	 * @param string $id
	 * @return string
	 */
	function getEmailById($id) {
		global $db;
		
		$sql = "SELECT email
				FROM " . DB_TBL_PREFIX . "users
				WHERE id = '{$id}'";
		$res = $db->getOne($sql);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Retrieve user status type
	 * based on user level
	 *
	 * @param integer $v
	 * @return string
	 */
	function getStatus($v = -1) {
		if($v == 0) {
			$status = $this->stati[0];
		} else if($v > 0 && $v < 100) {
			$status = $this->stati[1];
		} else if($v >= 100 && $v < 300) {
			$status = $this->stati[100];
		} else if($v >= 300) {
			$status = $this->stati[500];
		} else if($v < 0) {
			$status = "Deleted";
		}
		
		return $status;
	}
	
	/**
	 * Get textual status levels
	 *
	 * @return array
	 */
	function getStatusLevels() {
		return $this->stati;
	}
	
	/**
	 * Update user's information
	 *
	 * @param string $id
	 * @param string $fname
	 * @param string $lname
	 * @param string $email
	 */
	function updateInfo($id, $fname, $lname, $email) {
		global $db;
		
		$bool = true;
		
		$sql = "UPDATE " . DB_TBL_PREFIX . "users
				SET
					firstname = ?,
					lastname = ?,
					email = ?
				WHERE id= ?";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) {
			trigger_error($sth->getMessage(), E_USER_ERROR);
			
			$bool = false;
		}
		$res = $db->execute($sth, array(
			trim($fname), trim($lname), 
			trim($email), $id
		));
		if(DB::isError($res)) { 
			trigger_error($res->getDebugInfo(), E_USER_ERROR);
			
			$bool = false;
		}
		
		return $bool;
	}
	
	/**
	 * Retrieve a user's billing control ID
	 *
	 * @param string $uid
	 * @return integer False if not found
	 */
	function getBillingId($uid) {
		global $db;
	
		$sql = "SELECT id
				FROM " . DB_TBL_PREFIX . "user_billing
				WHERE user_id = '{$uid}'
				LIMIT 0,1";
		$res = $db->getOne($sql);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return (!is_null($res)) ? $res : false;
	}
	
	/**
	 * Update user's password
	 *
	 * @param string $user_id
	 * @param string $passwd
	 */
	function updatePassword($user_id, $passwd) {
		global $db;
		
		$sth = $db->prepare("UPDATE " . DB_TBL_PREFIX . "users SET password = MD5(?) WHERE id = ?");
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		$res = $db->execute($sth, array(trim($passwd), $user_id));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Retrieve all users by status level.
	 *
	 * @param integer $status_level
	 * @param string $order
	 * @param string $sort
	 * @return array
	 */
	function getAllByStatus($status_level = null, $order = "lastname", $sort = "ASC") {
		global $db;
		
		$sql = "SELECT *
				FROM " . DB_TBL_PREFIX . "users";
				
		if(!is_null($status_level)) {
			$sql .= " WHERE approved = '{$status_level}'";
		}
		
		$sql .= " ORDER BY {$order} {$sort}";
		
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
}


?>