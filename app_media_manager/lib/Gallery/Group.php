<?php
/**
 * @author C.J. Walsh <cj@perigeeglobal.com>
 * @copyright Copyright (c) 2008, C.J. Walsh
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://www.perigeeglobal.com
 * @package Gallery
 * @subpackage Gallery_Images
 */
class Gallery_Group {
	/**
	 * Select statement to be used across all queries
	 *
	 * @var string
	 */
	var $select_items;
	
	/**
	 * Collection of tables to be used in all queries
	 *
	 * @var string
	 */
	var $tables_items = "";

	/**
	 * Image object
	 *
	 * @var object
	 */
	var $image;
	
	/**
	 * Constructor
	 *
	 * @return Gallery_Group
	 */
	function Gallery_Group() {
		$this->image = new Gallery_Images();
		
		$this->select_items = "groups.*, 
						users.id AS uid, users.email, users.firstname, users.lastname, CONCAT(users.firstname, ' ', users.lastname) AS ufullname, 
						images.id AS img_id, images.title AS img_title, COUNT(images.id) AS total_num_images,
						(SELECT COUNT(*) FROM " . DB_TBL_PREFIX . "gallery_images WHERE approved = 1 AND group_id = groups.id) AS num_approved_images,
						gallery.id AS gallery_id, gallery.title AS gallery_title, gallery.description AS gallery_desc, gallery.parent_id AS gallery_parent, 
						gallery.created AS gallery_created,
						gallery2.id AS section_id, gallery2.title AS section_title, gallery2.description AS section_desc, gallery2.parent_id AS section_parent, 
						gallery2.created AS section_created";
		
		$this->tables_items = DB_TBL_PREFIX . "image_groups AS groups
				LEFT JOIN " . DB_TBL_PREFIX . "users AS users ON (users.id = groups.user_id)
				LEFT JOIN " . DB_TBL_PREFIX . "gallery_images AS images ON (images.group_id = groups.id)
				LEFT JOIN " . DB_TBL_PREFIX . "gallery_groups_cnx AS gallery_cnx ON (gallery_cnx.group_id = groups.id)
				LEFT JOIN " . DB_TBL_PREFIX . "galleries AS gallery ON (gallery.id = gallery_cnx.gallery_id)
				LEFT JOIN " . DB_TBL_PREFIX . "galleries AS gallery2 ON (gallery.parent_id = gallery2.id)";
	}
	
	/**
	 * Retrieves the number of images in a group
	 *
	 * @param integer $id Group's ID
	 * @return integer
	 */
	function getNumImages($id) {
		global $db;
	
		$sql = "SELECT COUNT(*) AS num 
				FROM ".DB_TBL_PREFIX."gallery_images
				WHERE group_id = '{$id}'
				AND deleted = 0";
		$res = $db->getOne($sql);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Inserts one group with just title info
	 *
	 * @param string $title
	 */
	function insertInitial($title) {
		global $db;
	
		$sql = "INSERT INTO " . DB_TBL_PREFIX . "image_groups (
					title, created, modified
				) VALUES (
					?, NOW(), NOW()
				)";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		$res = $db->execute($sth, array(trim($title)));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Get SELECT statement construction
	 *
	 * @return string
	 */
	function getSelectStatement() {
		return $this->select_items;
	}
	
	/**
	 * Get FROM statement construction
	 *
	 * @return unknown
	 */
	function getTablesStatement() {
		return $this->tables_items;
	}
	
	/**
	 * Retrieves on group by title
	 *
	 * @param string $title
	 * @return array
	 */
	function getOneByTitle($title) {
		global $db;
	
		$sql = "SELECT " . $this->getSelectStatement() . "
				FROM " . $this->getTablesStatement() . "
				WHERE groups.title = '{$title}'
				AND groups.deleted = 0
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				GROUP BY groups.id
				LIMIT 0,1";
		$res = $db->getRow($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Retrieves all image groups
	 * by user ID number, excluding
	 * images not assigned to a group
	 *
	 * @param string $uid User's ID
	 * @param string $order ORDER BY column
	 * @param string $sort Sort statement
	 * @return array
	 */
	function getAllByUser($uid, $order = "title", $sort = "ASC") {
		global $db;
	
		$sql = "SELECT " . $this->getSelectStatement() . "
				FROM " . $this->getTablesStatement() . "
				WHERE groups.user_id = '{$uid}'
				AND groups.deleted = 0
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				GROUP BY groups.id
				ORDER BY groups.{$order}
				{$sort}";	
		
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);

		return $res;
	}
	
	/**
	 * Retrieves all image groups
	 * by user, not excluding non-group images
	 *
	 * @param string $uid User's ID
	 * @param string $order ORDER BY column
	 * @param string $sort Sort statement
	 * @return array
	 */
	function getAllByUserComprehensive($uid, $order = "title", $sort = "ASC") {
		global $db;
	
		$sql = "SELECT " . $this->getSelectStatement() . "
				FROM " . $this->getTablesStatement() . "
				WHERE groups.user_id = '{$uid}'
				AND groups.deleted = 0
				GROUP BY groups.id
				ORDER BY groups.{$order}
				{$sort}";	
		
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);

		return $res;
	}
	
	/**
	 * Retrieves all unapproved image groups
	 * by user
	 *
	 * @param string $uid User's ID
	 * @param string $order ORDER BY column
	 * @param string $sort Sort statement
	 * @return array
	 */
	function getAllUnapprovedByUser($uid, $order = "title", $sort = "ASC") {
		global $db;
	
		$sql = "SELECT " . $this->getSelectStatement() . "
				FROM " . $this->getTablesStatement() . "
				WHERE groups.user_id = '{$uid}'
				AND groups.deleted = 0
				AND groups.approved = 0
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				GROUP BY groups.id
				ORDER BY groups.{$order}
				{$sort}";
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);

		return $res;
	}
	
	/**
	 * Retrieve all groups for a user
	 * taht have approved status.
	 *
	 * @param string $uid
	 * @param string $order
	 * @param string $sort
	 * @return array
	 */
	function getAllApprovedByUser($uid, $order = "title", $sort = "ASC") {
		global $db;
	
		$sql = "SELECT " . $this->getSelectStatement() . "
				FROM " . $this->getTablesStatement() . "
				WHERE groups.user_id = '{$uid}'
				AND groups.deleted = 0
				AND groups.approved = 1
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				GROUP BY groups.id
				ORDER BY groups.{$order}
				{$sort}";
		
		//echo $sql . "<br />";
		
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);

		return $res;
	}
	
	/**
	 * Inserts one group by user ID number with complete info:
	 * Title, start date, and end date.
	 *
	 * @param string $uid User's ID
	 * @param string $title
	 * @param string $start Start date
	 * @param string $end End date
	 */
	function insertFullByUser($uid, $title, $start, $end) {
		global $db;
	
		$endate = (!empty($end)) ? date("Y-m-d h:i:s", strtotime(trim($end))) : null;
		
		$sql = "INSERT INTO " . DB_TBL_PREFIX . "image_groups (
					user_id, title,
					date_start, date_end,
					created, modified
				) VALUES (
					?, ?,
					?, ?,
					NOW(), NOW()
				)";
				
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		$res = $db->execute($sth, array(
			$uid, trim($title),
			date("Y-m-d h:i:s", strtotime(trim($start))),
			$endate
		));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Insert one group by user ID number.
	 * Sets only the title and time it was created.
	 *
	 * @param string $uid User's ID
	 * @param string $title
	 */
	function insertByUser($uid, $title) {
		global $db;
	
		$sql = "INSERT INTO ".DB_TBL_PREFIX."image_groups (
					user_id, title, created, modified
				) VALUES (
					?, ?, NOW(), NOW()
				)";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		$res = $db->execute($sth, array($uid, trim($title)));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Retrieves a group by ID number
	 *
	 * @param integer $id Group's ID
	 * @return array
	 */
	function getOneById($id) {
		global $db;
	
		$sql = "SELECT " . $this->getSelectStatement() . "
				FROM " . $this->getTablesStatement() . "
				WHERE groups.id= '{$id}'
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				GROUP BY groups.id";
		$res = $db->getRow($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Deletes one group by ID number
	 *
	 * @param integer $id Group's ID
	 */
	function delete($id) {
		global $db;
	
		$sql = "UPDATE " . DB_TBL_PREFIX . "image_groups
				SET
					deleted = ?
				WHERE id= ?";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		$res = $db->execute($sth, array(1, $id));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Set status of groups to deleted belonging to user
	 *
	 * @param string $user_id
	 */
	function deleteAllByUser($user_id) {
		global $db;
		
		$sql = "UPDATE " . DB_TBL_PREFIX . "image_groups
				SET
					deleted = ?,
					approved = ?,
					modified = NOW()
				WHERE user_id = ?";
				
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		$res = $db->execute($sth, array(1, 0, $user_id));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Updates a group's title,
	 * start date, and end date.
	 *
	 * @param integer $id Group's ID
	 * @param string $title
	 * @param string $start Start date
	 * @param string $end End date
	 */
	function update($id, $title, $start, $end) {
		global $db;
	
		$start = trim($start);
		$end = trim($end);
	
		$sql = "UPDATE " . DB_TBL_PREFIX . "image_groups
				SET
					title = ?,
					date_start = ?,
					date_end = ?
				WHERE id = ?";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		$res = $db->execute($sth, array(
			trim($title),
			(strtotime($start) !== false) ? date("Y-m-d H:i:s", strtotime($start)) : null,
			(strtotime($end) !== false) ? date("Y-m-d H:i:s", strtotime($end)) : null,
			$id
		));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Retrieves all recently added
	 * groups that are pending approval.
	 *
	 * @param string $order MySQL ORDER BY column name
	 * @param string $sort MySQL sort statement
	 * @param string $limit Number of results to limit
	 * @return array
	 */
	function getRecentUnapproved($order = "title", $sort = "ASC", $limit = 10) {
		global $db;
	
		$sql = "SELECT " . $this->getSelectStatement() . "
				FROM " . $this->getTablesStatement() . "
				WHERE groups.approved = 0
				AND groups.deleted = 0
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				GROUP BY groups.id
				ORDER BY groups.{$order}
				{$sort}
				LIMIT 0,{$limit}";
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);

		return $res;
	}
	
	/**
	 * Retrieves all recently added
	 * groups that have been approved.
	 *
	 * @param string $order MySQL ORDER BY column name
	 * @param string $sort MySQL sort statement
	 * @param string $limit Number of results to limit
	 * @return array
	 */
	function getRecentApproved($order = "title", $sort = "ASC", $limit = 10) {
		global $db;
	
		$sql = "SELECT " . $this->getSelectStatement() . "
				FROM " . $this->getTablesStatement() . "
				WHERE groups.approved = 1
				AND groups.deleted = 0
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				GROUP BY groups.id
				ORDER BY groups.{$order}
				{$sort}
				LIMIT 0,{$limit}";
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);

		return $res;
	}
	
	/**
	 * Retrieves all groups that are approved.
	 *
	 * @param string $order MySQL ORDER BY column name
	 * @param string $sort MySQL sort statement
	 * @return array
	 */
	function getAllApproved($order = "title", $sort = "ASC") {
		global $db;
	
		$sql = "SELECT " . $this->getSelectStatement() . "
				FROM " . $this->getTablesStatement() . "
				WHERE groups.approved = 1
				AND groups.deleted = 0
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				ORDER BY groups.{$order}
				{$sort}";
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);

		return $res;
	}
	
	/**
	 * Retrieve all groups within a gallery.
	 *
	 * @param integer $gallery_id
	 * @return array
	 */
	function getAllByGallery($gallery_id, $order = 'title', $sort = 'ASC') {
		global $db;
	
		$sql = "SELECT " . $this->getSelectStatement() . "
				FROM " . DB_TBL_PREFIX . "gallery_groups_cnx AS cnx
				LEFT JOIN " . DB_TBL_PREFIX . "image_groups AS groups ON (groups.id = cnx.group_id)
				LEFT JOIN " . DB_TBL_PREFIX . "users AS users ON (users.id = groups.user_id)
				LEFT JOIN " . DB_TBL_PREFIX . "gallery_images AS images ON (images.group_id = groups.id)
				LEFT JOIN " . DB_TBL_PREFIX . "galleries AS gallery ON (gallery.id = cnx.gallery_id)
				LEFT JOIN " . DB_TBL_PREFIX . "galleries AS gallery2 ON (gallery.parent_id = gallery2.id)
				WHERE cnx.gallery_id = '{$gallery_id}'
				AND groups.deleted = 0
				AND groups.approved = 1
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				GROUP BY groups.id
				ORDER BY groups.{$order}
				{$sort}";
		
		//echo $sql . "<hr />";
				
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);

		return $res;
	}
	
	/**
	 * Retrieve a random list of groups.
	 *
	 * @param integer $limit
	 * @return array
	 */
	function getRandomApproved($limit = 10) {
		global $db;
	
		$sql = "SELECT " . $this->getSelectStatement() . "
				FROM " . $this->getTablesStatement() . "
				WHERE groups.approved = 1
				AND groups.deleted = 0
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				GROUP BY groups.id
				ORDER BY RAND()
				LIMIT 0,{$limit}";
				
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	function getRandomApprovedByGallery($gallery_id, $limit = 10) {
		global $db;
	
		$sql = "SELECT " . $this->getSelectStatement() . "
				FROM " . DB_TBL_PREFIX . "gallery_groups_cnx AS cnx
				LEFT JOIN " . DB_TBL_PREFIX . "image_groups AS groups ON (groups.id = cnx.group_id)
				LEFT JOIN " . DB_TBL_PREFIX . "users AS users ON (users.id = groups.user_id)
				LEFT JOIN " . DB_TBL_PREFIX . "gallery_images AS images ON (images.group_id = groups.id)
				LEFT JOIN " . DB_TBL_PREFIX . "galleries AS gallery ON (gallery.id = cnx.gallery_id)
				LEFT JOIN " . DB_TBL_PREFIX . "galleries AS gallery2 ON (gallery.parent_id = gallery2.id)
				WHERE gallery.id = '{$gallery_id}'
				AND groups.approved = 1
				AND groups.deleted = 0
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				GROUP BY groups.id
				ORDER BY RAND()
				LIMIT 0,{$limit}";
				
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Retrieve a group's section
	 *
	 * @param integer $group_id
	 * @return array
	 */
	function getSection($group_id) {
		global $db;
	
		$sql = "SELECT g2.*
				FROM " . DB_TBL_PREFIX . "gallery_groups_cnx AS cnx
				LEFT JOIN " . DB_TBL_PREFIX . "galleries AS g1
				ON (g1.id = cnx.gallery_id)
				LEFT JOIN " . DB_TBL_PREFIX . "galleries AS g2
				ON (g1.parent_id = g2.id)
				WHERE cnx.group_id = '{$group_id}'
				LIMIT 0,1";

		$res = $db->getRow($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Retrieve a group's gallery.
	 *
	 * @param integer $group_id
	 * @return array
	 */
	function getGallery($group_id) {
		global $db;
	
		$sql = "SELECT gallery.*
				FROM " . DB_TBL_PREFIX . "gallery_groups_cnx AS cnx
				LEFT JOIN " . DB_TBL_PREFIX . "galleries AS gallery
				ON (gallery.id = cnx.gallery_id)
				WHERE cnx.group_id = '{$group_id}'
				LIMIT 0,1";

		$res = $db->getRow($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Verify whether a group has a gallery
	 * assigned to it.
	 *
	 * @param integer $group_id
	 * @return boolean 
	 */
	function hasGallery($group_id) {
		global $db;
		
		$sql = "SELECT cnx.id AS id
				FROM " . DB_TBL_PREFIX . "gallery_groups_cnx AS cnx
				WHERE cnx.group_id = '{$group_id}'";
				
		$res = $db->getOne($sql);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return (is_null($res)) ? false : $res;
	}
	
	/**
	 * Retrieve all groups that are not featured.
	 *
	 * @param string $order
	 * @param string $sort
	 * @param integer $approved
	 * @param integer $deleted
	 * @return array
	 */
	function getUnfeatured($order = "title", $sort = "ASC", $approved = 1, $deleted = 0) {
		global $db;
		
		$sql = "SELECT " . $this->getSelectStatement() . "
				FROM " . $this->getTablesStatement() . "
				WHERE groups.approved = '{$approved}'
				AND groups.deleted = '{$deleted}'
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				AND groups.id NOT IN (SELECT group_id AS id FROM " . DB_TBL_PREFIX . "image_groups_featured)
				GROUP BY groups.id
				ORDER BY groups.{$order}
				{$sort}";
				
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Retrieve all featured groups.
	 *
	 * @param string $order
	 * @param string $sort
	 * @param integer $approved
	 * @param integer $deleted
	 * @return array
	 */
	function getFeatured($order = "title", $sort = "ASC", $approved = 1, $deleted = 0) {
		global $db;
		
		$sql = "SELECT " . $this->getSelectStatement() . "
				FROM " . $this->getTablesStatement() . "
				WHERE groups.approved = '{$approved}'
				AND groups.deleted = '{$deleted}'
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				AND images.approved = 1
				AND groups.id IN (SELECT group_id AS id FROM " . DB_TBL_PREFIX . "image_groups_featured)
				GROUP BY groups.id
				ORDER BY groups.{$order}
				{$sort}";
				
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
}
?>