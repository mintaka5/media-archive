<?php
/**
 * @author C.J. Walsh <cj@perigeeglobal.com>
 * @copyright Copyright (c) 2008, C.J. Walsh
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://www.perigeeglobal.com
 * @package Gallery_Admin
 * 
 */
class Gallery_Admin_Groups {
	/**
	 * Select statement constructor for MySQL
	 *
	 * @var string
	 */
	var $_selects;
	
	/**
	 * FROM statement constructor for MySQL
	 *
	 * @var string
	 */
	var $_tables;

	/**
	 * Constructor
	 *
	 * @return Gallery_Admin_Groups
	 */
	function Gallery_Admin_Groups() {
		$this->_selects = "groups.*, 
						users.id AS uid, users.email, users.firstname, users.lastname, CONCAT(users.firstname, ' ', users.lastname) AS ufullname, 
						images.id AS img_id, images.title AS img_title, COUNT(images.id) AS total_num_images,
						(SELECT COUNT(*) FROM " . DB_TBL_PREFIX . "gallery_images WHERE approved = 1 AND group_id = groups.id) AS num_approved_images,
						gallery.id AS gallery_id, gallery.title AS gallery_title, gallery.description AS gallery_desc, gallery.parent_id AS gallery_parent, 
						gallery.created AS gallery_created,
						gallery2.id AS section_id, gallery2.title AS section_title, gallery2.description AS section_desc, gallery2.parent_id AS section_parent, 
						gallery2.created AS section_created";
		
		$this->_tables = DB_TBL_PREFIX . "image_groups AS groups
				LEFT JOIN " . DB_TBL_PREFIX . "users AS users ON (users.id = groups.user_id)
				LEFT JOIN " . DB_TBL_PREFIX . "gallery_images AS images ON (images.group_id = groups.id)
				LEFT JOIN " . DB_TBL_PREFIX . "gallery_groups_cnx AS gallery_cnx ON (gallery_cnx.group_id = groups.id)
				LEFT JOIN " . DB_TBL_PREFIX . "galleries AS gallery ON (gallery.id = gallery_cnx.gallery_id)
				LEFT JOIN " . DB_TBL_PREFIX . "galleries AS gallery2 ON (gallery.parent_id = gallery2.id)";
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
	 * Get SELECT statement construction
	 *
	 * @return string
	 */
	function getSelects() {
		return $this->_selects;
	}
	
	/**
	 * Retrieve all groups from database.
	 *
	 * @param string $order
	 * @param string $sort
	 * @return array
	 */
	function getAll($order = "title", $sort = "ASC") {
		global $db;
		
		$sql = "SELECT " . $this->getSelects() . "
				FROM " . $this->getTables() . "
				WHERE images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				GROUP BY groups.id
				ORDER BY groups.{$order}
				{$sort}";
				
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Retrieve all groups for a particualr user
	 * from the database.
	 *
	 * @param string $user_id
	 * @param string $order
	 * @param string $sort
	 * @return array
	 */
	function getAllByUser($user_id, $order = "title", $sort = "ASC") {
		global $db;
		
		$sql = "SELECT " . $this->getSelects() . "
				FROM " . $this->getTables() . "
				WHERE groups.user_id = '{$user_id}'
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				GROUP BY groups.id
				ORDER BY groups.{$order}
				{$sort}";
				
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Update gallery - group relationship.
	 *
	 * @param integer $cnx_id
	 * @param integer $gallery_id
	 */
	function updateGallery($cnx_id, $gallery_id) {
		global $db;
		
		$sql = "UPDATE " . DB_TBL_PREFIX . "gallery_groups_cnx
				SET
					gallery_id = ?
				WHERE id = ?";
				
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		$res = $db->execute($sth, array($gallery_id, $cnx_id));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Insert a new gallery - group relationship.
	 *
	 * @param integer $group_id
	 * @param integer $gallery_id
	 */
	function insertGallery($group_id, $gallery_id) {
		global $db;
		
		$sql = "INSERT INTO " . DB_TBL_PREFIX . "gallery_groups_cnx (
					gallery_id, group_id
				) VALUES (
					?, ?
				)";
				
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		$res = $db->execute($sth, array($gallery_id, $group_id));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Deny/disapprove a group/event
	 *
	 * @param integer $group_id
	 */
	function deny($group_id) {
		global $db;
	
		$sql = "UPDATE " . DB_TBL_PREFIX . "image_groups
				SET
					approved = ?,
					deleted = ?,
					modified = NOW()
				WHERE id = ?";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		$res = $db->execute($sth, array(0, 1, $group_id));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Approve a group
	 *
	 * @param integer $group_id
	 */
	function approve($group_id) {
		global $db;
	
		$sql = "UPDATE " . DB_TBL_PREFIX . "image_groups
				SET
					approved = ?,
					deleted = ?,
					modified = NOW()
				WHERE id = ?";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		$res = $db->execute($sth, array(1, 0, $group_id));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Set undeleted status of event. 
	 *
	 * @param integer $group_id
	 */
	function recover($group_id) {
		global $db;
		
		$sql = "UPDATE " . DB_TBL_PREFIX . "image_groups
				SET
					deleted = ?
				WHERE id = ?";
				
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		$res = $db->execute($sth, array(0, $group_id));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
}
?>