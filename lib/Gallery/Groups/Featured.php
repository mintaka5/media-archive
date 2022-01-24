<?php
/**
 * @author C.J. Walsh <cj@perigeeglobal.com>
 * @copyright Copyright (c) 2008, C.J. Walsh
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://www.perigeeglobal.com
 * @package Gallery_Groups
 * 
 */
class Gallery_Groups_Featured {
	/**
	 * SELECT statement constructor
	 *
	 * @var string
	 */
	var $_selects = "groups.*, 
						users.id AS uid, users.email, users.firstname, users.lastname, CONCAT(users.firstname, ' ', users.lastname) AS ufullname, 
						images.id AS img_id, images.title AS img_title, COUNT(*) as num_images,
						gallery.id AS gallery_id, gallery.title AS gallery_title, gallery.description AS gallery_desc, gallery.parent_id AS gallery_parent, 
						gallery.created AS gallery_created,
						gallery2.id AS section_id, gallery2.title AS section_title, gallery2.description AS section_desc, gallery2.parent_id AS section_parent, 
						gallery2.created AS section_created";
						
	/**
	 * FROM statement constructor
	 *
	 * @var string
	 */
	var $_tables;

	/**
	 * Constructor
	 *
	 * @return Gallery_Groups_Featured
	 */
	function Gallery_Groups_Featured() {
		$this->_tables = DB_TBL_PREFIX . "image_groups_featured AS featured
				LEFT JOIN " . DB_TBL_PREFIX . "image_groups AS groups ON (groups.id = featured.group_id)
				LEFT JOIN " . DB_TBL_PREFIX . "users AS users ON (users.id = groups.user_id)
				LEFT JOIN " . DB_TBL_PREFIX . "gallery_images AS images ON (images.group_id = groups.id)
				LEFT JOIN " . DB_TBL_PREFIX . "gallery_groups_cnx AS gallery_cnx ON (gallery_cnx.group_id = groups.id)
				LEFT JOIN " . DB_TBL_PREFIX . "galleries AS gallery ON (gallery.id = gallery_cnx.gallery_id)
				LEFT JOIN " . DB_TBL_PREFIX . "galleries AS gallery2 ON (gallery.parent_id = gallery2.id)";
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
	 * Retrieve all featured groups.
	 *
	 * @param string $order
	 * @param string $sort
	 * @param integer $approved
	 * @param integer $deleted
	 * @return array
	 */
	function getAll($order = "title", $sort = "ASC", $approved = 1, $deleted = 0) {
		global $db;
		
		$sql = "SELECT " . $this->getSelects() . "
				FROM " . $this->getTables() . "
				WHERE groups.approved = '{$approved}'
				AND groups.deleted = '{$deleted}'
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				GROUP BY groups.id
				ORDER BY groups.{$order}
				{$sort}";
				
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Set group as featured.
	 *
	 * @param integer $group_id
	 * @param string $expires
	 */
	function setFeatured($group_id, $expires = null) {
		global $db;
		
		$sql = "INSERT INTO " . DB_TBL_PREFIX . "image_groups_featured (
					group_id, expires
				) VALUES (
					?, ?
				)";
				
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		$res = $db->execute($sth, array(
			$group_id,
			(is_null($expires)) ? strtotime("+4 weeks") : strtotime($expires)
		));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Remove group from featured status.
	 *
	 * @param integer $group_id
	 */
	function unsetFeatured($group_id) {
		global $db;
		
		$sql = "DELETE FROM " . DB_TBL_PREFIX . "image_groups_featured WHERE group_id = ?";
		
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		$res = $db->execute($sth, array($group_id));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
}
?>