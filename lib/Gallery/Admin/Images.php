<?php
/**
 * @author C.J. Walsh <cj@perigeeglobal.com>
 * @copyright Copyright (c) 2008, C.J. Walsh
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://www.perigeeglobal.com
 * @package Gallery
 * @subpackage Gallery_Admin
 */
class Gallery_Admin_Images {
	/**
	 * SELECT statement constructor
	 *
	 * @var string
	 */
	var $_selects = "
		images.id AS id, 
		images.title AS title, 
		images.description AS description, 
		images.location AS location, 
		images.lat AS lat, 
		images.lng AS lat, 
		images.created AS img_created,
		images.approved AS img_approved, 
		images.deleted AS img_deleted,
		images.upload_complete AS is_complete,
		users.id AS uid, 
		users.firstname, 
		users.lastname,
		users.email AS uemail, 
		CONCAT(users.firstname, ' ', users.lastname) AS ufullname, 
		users.approved AS uapproved,
		groups.id AS group_id, 
		groups.title AS group_title, 
		groups.date_start AS date_start, 
		groups.date_end AS date_end, 
		groups.created AS group_created
	";
	
	/**
	 * FROM statement constructor
	 *
	 * @var string
	 */
	var $_tables;

	/**
	 * Constructor
	 *
	 * @return Gallery_Admin_Images
	 */
	function Gallery_Admin_Images() {
		$this->_tables = DB_TBL_PREFIX . "gallery_images AS images
				LEFT JOIN " . DB_TBL_PREFIX . "image_groups AS groups ON (groups.id = images.group_id)
				LEFT JOIN " . DB_TBL_PREFIX . "users AS users ON (users.id = images.user_id)";
	}
	
	/**
	 * Get SELECT statement construction
	 *
	 * @return string
	 */
	function getSelects() {
		return $this->_selects;
	}
	
	/**
	 * Get FROM statement construction
	 *
	 * @return string
	 */
	function getTables() {
		return $this->_tables;
	}
	
	/**
	 * Retrieve all images fro a user.
	 *
	 * @param string $user_id
	 * @param string $order
	 * @param string $sort
	 * @return array
	 */
	function getAllByUser($user_id, $order = "title", $sort = "ASC") {
		global $db;
		
		$sql = "SELECT" . $this->getSelects() . "
				FROM " . $this->getTables() . "
				WHERE users.id = '{$user_id}'
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				ORDER BY images.{$order}
				{$sort}";
				
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Retrieve all images that have undeleted status.
	 *
	 * @param string $order
	 * @param string $sort
	 * @return array
	 */
	function getAllUndeleted($order = "title", $sort = "ASC") {
		global $db;
		
		$sql = "SELECT " . $this->getSelects() . "
				FROM " . $this->getTables() . "
				WHERE images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				AND images.deleted = 0
				ORDER BY images.{$order}
				{$sort}";
		
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Retrieve all images within a group.
	 *
	 * @param integer $group_id
	 * @param string $order
	 * @param string $sort
	 * @return array
	 */
	function getAllByGroup($group_id, $order = "title", $sort = "ASC") {
		global $db;
		
		$sql = "SELECT " . $this->getSelects() . "
				FROM " . $this->getTables() . "
				WHERE images.group_id = '{$group_id}'
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				ORDER BY images.{$order}
				{$sort}";
				
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Set status of image to undeleted.
	 *
	 * @param string $image_id
	 */
	function recover($image_id) {
		global $db;
		
		$sql = "UPDATE " . DB_TBL_PREFIX . "gallery_images
				SET
					deleted = ?,
					modified = NOW()
				WHERE id = ?";
				
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		$res = $db->execute($sth, array(0, $image_id));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Approve a single image
	 *
	 * @param string $img_id
	 */
	function approve($img_id) {
		global $db;
	
		$sql = "UPDATE " . DB_TBL_PREFIX . "gallery_images
				SET
					approved = ?,
					modified = NOW()
				WHERE id = ?";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->Message(), E_USER_ERROR);
		
		$res = $db->execute($sth, array(1, $img_id));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Deny/unapprove an image.
	 *
	 * @param string $img_id
	 */
	function deny($img_id) {
		global $db;
	
		$sql = "UPDATE " . DB_TBL_PREFIX . "gallery_images
				SET
					approved = ?,
					deleted = ?,
					modified = NOW()
				WHERE id = ?";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->Message(), E_USER_ERROR);
		
		$res = $db->execute($sth, array(0, 1, $img_id));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Deny all images within a group.
	 *
	 * @param integer $group_id
	 */
	function denyGroup($group_id) {
		global $db;
	
		$sql = "UPDATE " . DB_TBL_PREFIX . "gallery_images
				SET
					approved = ?,
					deleted = ?,
					modified = NOW()
				WHERE group_id = ?";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->Message(), E_USER_ERROR);
		
		$res = $db->execute($sth, array(0, 1, $group_id));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Approve all images within a group
	 *
	 * @param integer $group_id
	 */
	function approveGroup($group_id) {
		global $db;
		
		$sql = "UPDATE " . DB_TBL_PREFIX . "gallery_images
				SET
					approved = ?,
					deleted = ?,
					modified = NOW()
				WHERE group_id = ?";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		$res = $db->execute($sth, array(1, 0, $group_id));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Set status og images within a group to undeleted.
	 *
	 * @param integer $group_id
	 */
	function recoverGroup($group_id) {
		global $db;
		
		$sql = "UPDATE " . DB_TBL_PREFIX . "gallery_images
				SET
					deleted = ?
				WHERE group_id = ?";
		
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		$res = $db->execute($sth, array(0, $group_id));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
}
?>