<?php
require_once("Gallery/Images.php");
require_once("Gallery/Group.php");

/**
 * @author C.J. Walsh <cj@perigeeglobal.com>
 * @copyright Copyright (c) 2008, C.J. Walsh
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://www.perigeeglobal.com
 * @package Gallery
 * @subpackage Gallery_Images
 * @subpackage Gallery_Group
 */
class Gallery {
	/**
	 * Gallery_Images
	 *
	 * @var object
	 */
	 
	var $image;
	/**
	 * Gallery_Group class
	 *
	 * @var object
	 */
	var $group;
	
	/**
	 * Constructor
	 *
	 * @return Gallery
	 */
	function Gallery() {
		$this->image = new Gallery_Images();
		$this->group = new Gallery_Group();
	}
	
	/**
	 * Formats data array for the gallery
	 *
	 * @param array $data
	 * @return array
	 */
	function set($data) {
		$ary = array();
		
		if(!empty($data)) {
			foreach($data as $k => $v) {
				$ary[$k] = $v;
			}
			
			$ary['events'] = $this->group->getAllByGallery($data['id']);
			$ary['random_events'] = $this->group->getRandomApprovedByGallery($data['id']);
		}
		
		return $ary;
	}
	
	/**
	 * Retrieve all groups under parent
	 *
	 * @param integer $pid Parent gallery ID
	 * @param string $order MySQL ORDER BY column name
	 * @param string $sort MySQL sort statement
	 * @return array
	 */
	function getAllByParent($pid, $order = "title", $sort= "ASC") {
		global $db;
	
		$sql = "SELECT * FROM ".DB_TBL_PREFIX."galleries
			WHERE parent_id = '{$pid}'
			ORDER BY {$order}
			{$sort}";
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		$ary = array();
		if(!empty($res)) {
			foreach($res as $k => $v) {
				$ary[] = $this->set($v);
			}
		}
		
		return $ary;
	}
	
	/**
	 * Retrieve one gallery by ID
	 *
	 * @param integer $id Gallery's ID
	 * @return array
	 */
	function getOne($id) {
		global $db;
	
		$sql = "SELECT * FROM ".DB_TBL_PREFIX."galleries
			WHERE id = '{$id}'";
		$res = $db->getRow($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $this->set($res);
	}
	
	/**
	 * Retrieve all galleries
	 *
	 * @param string $order MySQL ORDER BY column name
	 * @param string $sort MySQL sort statement
	 * @return array
	 */
	function getAll($order = "title", $sort = "ASC") {
		global $db;
	
		$sql = "SELECT * FROM ".DB_TBL_PREFIX."galleries
				WHERE parent_id > 0
				AND deleted = 0
				ORDER BY {$order}
				{$sort}";
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		$ary = array();
		if(!empty($res)) {
			foreach($res as $k => $v) {
				$ary[] = $this->set($v);
			}
		}
		
		return $ary;
	}
	
	/**
	 * Retrieve all of a gallery's images
	 *
	 * @param integer $gallery_id
	 * @param integer $limit
	 * @return array
	 */
	function getAllImagesOfParent($gallery_id, $limit = 20, $order = "galleries.title", $sort = "ASC") {
		global $db;
	
		$sql = "SELECT images.id
				FROM " . DB_TBL_PREFIX . "gallery_images AS images
				LEFT JOIN " . DB_TBL_PREFIX . "image_groups AS groups
				ON (groups.id = images.group_id)
				LEFT JOIN " . DB_TBL_PREFIX . "gallery_groups_cnx AS cnx
				ON (cnx.group_id = groups.id)
				LEFT JOIN " . DB_TBL_PREFIX . "galleries AS galleries
				ON (galleries.id = cnx.gallery_id)
				WHERE galleries.parent_id = '{$gallery_id}'
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				AND images.deleted = 0
				ORDER BY {$order}
				{$sort}
				LIMIT 0,{$limit}";
		$res = $db->getCol($sql);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		$ary = array();
		if(!empty($res)) {
			foreach($res as $v) {
				$ary[] = $this->image->getOneById($v);
			}
		}
		
		return $ary;
	}
	
	/**
	 * Set gallery to deleted status.
	 *
	 * @param integer $group_id
	 */
	function delete($group_id) {
		global $db;
		
		$sql = "UPDATE " . DB_TBL_PREFIX . "galleries
				SET
					deleted = ?
				WHERE id = ?";
				
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		$res = $db->execute($sth, array(1, $group_id));
		if(DB::isError($res)) trigger_error($res->getMessage(), E_USER_ERROR);
		
		return;
	}
	
	function titleExists($title) {
		global $db;
		
		$sql = "SELECT title
				FROM " . DB_TBL_PREFIX . "galleries
				WHERE title = '{$title}'";
		
		$res = $db->getOne($sql);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return (is_null($res)) ? false : $res;
	}
	
	function getIdByTitle($title) {
		global $db;
		
		$sql = "SELECT id
				FROM " . DB_TBL_PREFIX . "galleries
				WHERE title = '{$title}'";
		
		$res = $db->getOne($sql);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	function getTitleById($gallery_id) {
		global $db;
		
		$sql = "SELECT title
				FROM " . DB_TBL_PREFIX . "galleries
				WHERE id = '{$gallery_id}'";
		
		$res = $db->getOne($sql);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
}
?>