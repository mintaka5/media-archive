<?php
require_once("Gallery.php");
require_once("users/Admin.php");
require_once("Gallery/Admin/Images.php");
require_once("Gallery/Admin/Groups.php");

/**
 * @author C.J. Walsh <cj@perigeeglobal.com>
 * @copyright Copyright (c) 2008, C.J. Walsh
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://www.perigeeglobal.com
 * @package Gallery
 * @subpackage Users_Admin
 */
class Gallery_Admin extends Gallery {
	/**
	 * User's instance
	 *
	 * @var object
	 */
	var $users;
	
	/**
	 * Images administration instance
	 *
	 * @var object
	 */
	var $_admin_image;
	
	/**
	 * Group administration instance
	 *
	 * @var object
	 */
	var $_admin_group;

	/**
	 * Constructor
	 *
	 * @return Gallery_Admin
	 */
	function Gallery_Admin() {
		$this->Gallery();
		
		$this->users = new Users_Admin();
		
		$this->_admin_image = new Gallery_Admin_Images();
		
		$this->_admin_group = new Gallery_Admin_Groups();
	}
	
	/**
	 * Updates gallery's title
	 *
	 * @param integer $gid Gallery's ID
	 * @param string $title
	 */
	function updateTitle($gid, $title) {
		global $db;
		
		$sql = "UPDATE ".DB_TBL_PREFIX."galleries
			SET title = ?
			WHERE id = ?";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		$res = $db->execute($sth, array(trim($title), $gid));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Assigns a photo to a gallery
	 *
	 * @param integer $gid Gallery's ID
	 * @param string $pid Image's ID
	 * @return boolean
	 */
	function assign($gid, $pid) {
		global $db;
		
		$bool = true;
		
		$sql = "INSERT INTO ".DB_TBL_PREFIX."gallery_images_cnx (
					gallery_id, image_id
				) VALUES (
					?, ?
				)";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) $sth->getMessage();
		$res = $db->execute($sth, array($gid, $pid));
		if(DB::isError($res)) $res->getDebugInfo();
		
		return $bool;
	}
	
	/**
	 * Retrieves all unapproved photos
	 *
	 * @param string $order MySQL ORDER BY column name
	 * @param string $sort MySQL sort statement
	 * @return array
	 */
	function getUnapproved($order = 'created', $sort = 'DESC') {
		global $db;
		
		$sql = "SELECT * FROM " . DB_TBL_PREFIX . "gallery_images
				WHERE approved = 0
				AND deleted = 0
				AND upload_complete = 1";
		
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		$ary = array();
		if(!empty($res)) {
			foreach($res as $v) {
				$ary[] = $this->image->setImage($v);
			}
		}
		
		return $ary;
	}
	
	/**
	 * Insert a sub-gallery into database
	 *
	 * @param integer $pid Parent gallery ID
	 * @param string $title
	 */
	function insertSub($pid, $title) {
		global $db;
	
		$sql = "INSERT INTO ".DB_TBL_PREFIX."galleries (
				title, parent_id, created
			) VALUES (
				?, ?, NOW()
			)";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		$res = $db->execute($sth, array($title, $pid));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
	}
	
	/**
	 * Insert new gallery into database
	 *
	 * @param string $title
	 * @param string $desc Description
	 * @param integer $parent_id
	 */
	function insert($title, $desc, $parent_id = 0) {
		global $db;
		
		$sql = "INSERT INTO " . DB_TBL_PREFIX . "galleries (
					title, description, parent_id, created
				) VALUES (
					?, ?, ?, NOW()
				)";
				
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		$res = $db->execute($sth, array(
			trim($title), trim($desc), $parent_id
		));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Update gallery data.
	 *
	 * @param integer $gallery_id
	 * @param string $title
	 * @param string $desc Description
	 * @param integer $section_id Collection/Parent ID
	 */
	function update($gallery_id, $title, $desc, $section_id) {
		global $db;
		
		$sql = "UPDATE " . DB_TBL_PREFIX . "galleries
				SET
					title = ?,
					description = ?,
					parent_id = ?,
					modified = NOW()
				WHERE id= ?";
				
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		$res = $db->execute($sth, array(
			trim($title),
			trim($desc),
			$section_id,
			$gallery_id
		));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
}
?>