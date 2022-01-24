<?php
require_once("users.php");
require_once("HTTP/Upload.php");
require_once("Gallery/Images/Keywords.php");

/**
 * Manages image information, including
 * database interaction, adn image manipulation.
 *
 * @author C.J. Walsh <cj@perigeeglobal.com>
 * @copyright Copyright (c) 2008, C.J. Walsh
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://www.perigeeglobal.com
 * @package Gallery
 * @subpackage Gallery_Images_Keywords
 * @subpackage Users
 */
class Gallery_Images {
	var $extension_map = array();
	/**
	 * SELECT statement constructor
	 *
	 * @var string
	 */
	var $select_items = "images.id, images.title, images.description, images.location, images.lat, images.lng, images.created AS img_created,
							DATE_FORMAT(images.created, '%M %e, %Y') AS formatted_img_created,
							images.approved AS img_approved, images.deleted AS img_deleted, images.upload_complete AS is_complete,
							users.id AS uid, users.firstname, users.lastname, users.email AS uemail, CONCAT(users.firstname, ' ', users.lastname) AS ufullname, users.approved AS uapproved,
							groups.id AS group_id, groups.title AS group_title, groups.date_start, groups.date_end, groups.approved AS group_approved, groups.created AS group_created,
							gallery.id AS gallery_id, gallery.title AS gallery_title, gallery.description AS gallery_desc, gallery.parent_id AS gallery_parent, 
							gallery.created AS gallery_created,
							gallery2.id AS section_id, gallery2.title AS section_title, gallery2.description AS section_desc, gallery2.parent_id AS section_parent, 
							gallery2.created AS section_created";
	
	/**
	 * FROM statement constructor
	 *
	 * @var string
	 */
	var $table_items = "";

	/**
	 * User
	 *
	 * @var object Associates user to photo object
	 */
	var $user;
	
	/**
	 * Keywords instance
	 *
	 * @var object
	 */
	var $keyword;
	
	/**
	 * Constructor
	 *
	 * @return Gallery_Images
	 */
	function Gallery_Images() {
		$this->user = new Users();
		
		$this->setExtensionMap();
		
		$this->keyword = new Gallery_Images_Keywords();
		
		$this->table_items = DB_TBL_PREFIX . "gallery_images AS images
				LEFT JOIN " . DB_TBL_PREFIX . "image_groups AS groups ON (groups.id = images.group_id)
				LEFT JOIN " . DB_TBL_PREFIX . "gallery_groups_cnx AS gallery_cnx ON (gallery_cnx.group_id = images.group_id)
				LEFT JOIN " . DB_TBL_PREFIX . "galleries AS gallery ON (gallery.id = gallery_cnx.gallery_id)
				LEFT JOIN " . DB_TBL_PREFIX . "galleries AS gallery2 ON (gallery.parent_id = gallery2.id)
				LEFT JOIN " . DB_TBL_PREFIX . "users AS users ON (users.id = images.user_id)";
	}
	
	function setExtensionMap() {
		$this->extension_map[] = array('mime_type' => 'image/jpeg', 'ext' => 'jpg');
		$this->extension_map[] = array('mime_type' => 'image/tiff', 'ext' => 'tiff');
		$this->extension_map[] = array('mime_type' => 'image/gif', 'ext' => 'gif');
		$this->extension_map[] = array('mime_type' => 'image/png', 'ext' => 'png');
	}
	
	function getExtensionMap() {
		return $this->extension_map;
	}
	
	function getExtensionByMimetype($mime_type = '') {
		$em = $this->getExtensionMap();
		
		$ext = null;
		foreach($em as $v) {
			if($mime_type == $v['mime_type']) {
				$ext = $v['ext'];
				break;
			}
		}
		
		if(is_null($ext)) {
			trigger_error('No extension available for the mime-type: ' . $mime_type . '.', E_USER_ERROR);
		}
		
		return $ext;
	}

	/**
	 * Sets image data and all related info
	 *
	 * @param array $row
	 * @return array
	 */
	
	function getEXIF($img_id) {
		$img = $this->getFileDataById($img_id);
		
		$ary = array();
		
		/**
		 * add exif data to file:
		 * create temp file -> write binary 
		 * data from db to file -> acquire exif 
		 * data from file -> populate exif data to array ->
		 * finally delete temp file
		 */
		$tmpfile = tempnam(APP_TMP_DIR, "exif_");
		$handle = fopen($tmpfile, "w");
		fwrite($handle, $img);
		fclose($handle);
		exec(EXIF_TOOL . " " . $tmpfile, $output);
		$ary['exif'] = array();
		
		foreach($output as $v) {
			$kv = preg_split("#[\t\s]+\:\s#", $v);
			$ek = trim(strtolower(preg_replace("#[\t\s\W]+#", "_", $kv[0])));
			$ev = trim($kv[1]);
			$ary['exif'][$ek] = $ev; 
		}
		
		/**
		 * Need to gather dimensions, and image size.
		 */
		$filesize = array_reduce(
			array(" B", " KB", " MB"),
			create_function('$a, $b', 'return is_numeric($a) ? ($a >= 1024 ? $a / 1024 : number_format($a, 2) . $b) : $a;'),
			filesize($tmpfile)
		);
		$ary['filesize'] = $filesize;
		
		$pxdimensions = getimagesize($tmpfile);
		$ary['width']['px'] = $pxdimensions[0];
		$ary['height']['px'] = $pxdimensions[1];
		$ary['width']['in'] = round($pxdimensions[0] / $ary['exif']['x_resolution'], 0);
		$ary['height']['in'] = round($pxdimensions[1] / $ary['exif']['y_resolution'], 0);
		
		unlink($tmpfile);
		unset($tmpfile);
		
		return $ary;
	} 
	
	/**
	 * Get binary file data for an image.
	 *
	 * @param unknown_type $img_id
	 * @return unknown
	 */
	function getFileDataById($img_id) {
		global $db;
		
		$sql = "SELECT file
				FROM " . DB_TBL_PREFIX . "gallery_images
				WHERE id = '{$img_id}'";
		$res = $db->getOne($sql);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
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
	 * @return string
	 */
	function getTablesStatement() {
		return $this->table_items;
	}
	
	/**
	 * Retrieve an image by ID
	 *
	 * @param string $id Image ID
	 * @return array
	 */
	function getOneById($id) {
		global $db;
		
		$sql = "SELECT " . $this->getSelectStatement() . "	
				FROM " . $this->getTablesStatement() . "
				WHERE images.id = '{$id}'";
		
		//echo $sql; die();
		
		$res = $db->getRow($sql , DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
 	}
	
	/**
	 * Insert an image to the database by user
	 *
	 * @param string $file Binary file data stream
	 * @param string $user User's ID
	 */
	function insert($file, $user) {
		global $db;
	
		$imgid = preg_replace("#[\W]+#", "", uniqid("", true));
	
		$sql = "INSERT INTO ". DB_TBL_PREFIX . "gallery_images (
				id, file, user_id, created, modified
			) VALUES (
				?, ?, ?, NOW(), NOW()
			)";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		$res = $db->execute($sth, array($imgid, $file, $user));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $imgid;
	}

	/**
	 * Insert a new image with a group assignment.
	 *
	 * @param byte $file
	 * @param string $user
	 * @param integer $group_id
	 * @return string
	 */
	function insertWithGroup($file, $user, $group_id) {
		global $db;
	
		$imgid = preg_replace("#[\W]+#", "", uniqid("", true));
	
		$sql = "INSERT INTO ". DB_TBL_PREFIX . "gallery_images (
				id, file, user_id, group_id, created, modified
			) VALUES (
				?, ?, ?, ?, NOW(), NOW()
			)";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		$res = $db->execute($sth, array($imgid, $file, $user, $group_id));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $imgid;
	}
	
	/**
	 * Update an image's information
	 *
	 * @param string $id Image's ID
	 * @param string $title
	 * @param integer $group Images' group ID
	 * @param string $desc Description
	 * @param string $loc Location of where the image was taken
	 */
	function update($id, $title, $group, $desc, $loc) {
		global $db;
	
		$sql = "UPDATE ". DB_TBL_PREFIX."gallery_images
			SET
				title = ?,
				group_id = ?,
				description = ?,
				location = ?,
				upload_complete = 1,
				modified = NOW()
			WHERE id = ?";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		$res = $db->execute($sth, array(
			trim($title), 
			$group,
			trim($desc), 
			trim($loc),  
			$id
		));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
	}
	
	/**
	 * Updates image's information,
	 * without effecting group
	 *
	 * @param string $id Image's ID
	 * @param string $title 
	 * @param string $desc Description
	 * @param string $location Location of where the image was taken
	 * @return boolean
	 */
	function updateAll($id, $title, $desc, $location) {
		$bool = true;
		
		$sql = "UPDATE ".DB_TBL_PREFIX."gallery_images
			SET
				title = ?,
				description = ?,
				location = ?,
				upload_complete = 1,
				modified = NOW()
			WHERE id = ?";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) $bool = false;
		global $db; $res = $db->execute($sth, array(
			trim($title), trim($desc), trim($location), $id
		));
		if(DB::isError($res)) $bool = false;
		
		return $bool;
	}
	
	/**
	 * Retrieve images that have an incomplete upload status
	 * by user ID
	 *
	 * @param string $uid User's ID
	 * @return array
	 */
	function getIncompleteByUser($uid, $limit = null) {
		global $db;
	
		$sql = "SELECT " . $this->getSelectStatement() . "	
				FROM " . $this->getTablesStatement() . "
				WHERE images.user_id = '{$uid}'
				AND images.upload_complete = 0
				AND images.deleted = 0
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)";
				
		if(!is_null($limit)) {
			$sql .= " LIMIT 0,{$limit}";
		}
				
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Retrieve all images by user ID
	 *
	 * @param string $uid User's ID
	 * @return array
	 */
	function getAllByUser($uid, $limit = null) {
		global $db;
	
		$sql = "SELECT " . $this->getSelectStatement() . "	
				FROM " . $this->getTablesStatement() . "
				WHERE images.user_id = '{$uid}'
				AND images.deleted = 0
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)";
				
		if(!is_null($limit)) {
			$sql .= " LIMIT 0,{$limit}";
		}
				
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Get one image for a group
	 *
	 * @param integer $gid Group's ID
	 * @param string $order MySQL ORDER BY column name
	 * @param string $sort MySQL sort statement
	 * @return array
	 */
	function getOneByGroup($gid, $order = "title", $sort = "ASC") {
		global $db;
	
		$sql = "SELECT " . $this->getSelectStatement() . "	
				FROM " . $this->getTablesStatement() . "
				WHERE images.group_id = '{$gid}'
				AND images.deleted = 0
				AND images.upload_complete = 1
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				ORDER BY images.{$order}
				{$sort}
				LIMIT 0,1";
		$res = $db->getOne($sql);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Retrieve all images by group ID number
	 *
	 * @param integer $id Group's ID
	 * @param string $order MySQL ORDER BY column name
	 * @param string $sort MySQL sort statement
	 * @return array
	 */
	function getAllByGroup($gid, $order = "title", $sort = "ASC") {
		global $db;
	
		$sql = "SELECT " . $this->getSelectStatement() . "	
				FROM " . $this->getTablesStatement() . "
				WHERE images.group_id = '{$gid}'
				AND images.deleted = 0
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				ORDER BY images.{$order}
				{$sort}";
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Retrieve all images by user and group.
	 *
	 * @param integer $group_id
	 * @param string $user_id
	 * @param string $order
	 * @param string $sort
	 * @return array
	 */
	function getAllByGroupAndUser($group_id, $user_id, $order = "title", $sort = "ASC") {
		global $db;
		
		$sql = "SELECT " . $this->getSelectStatement() . "
				FROM " . $this->getTablesStatement() . "
				WHERE images.group_id = '{$group_id}'
				AND users.id = '{$user_id}'
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				ORDER BY images.{$order}
				{$sort}";
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	function getAllNotRemovedByGroupAndUser($group_id, $user_id, $order = "title", $sort = "ASC") {
		global $db;
		
		$sql = "SELECT " . $this->getSelectStatement() . "
				FROM " . $this->getTablesStatement() . "
				WHERE images.group_id = '{$group_id}'
				AND users.id = '{$user_id}'
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				AND images.deleted = 0
				ORDER BY images.{$order}
				{$sort}";
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Retrieve all images by group ID number
	 * taht are approved
	 *
	 * @param integer $id Group's ID
	 * @param string $order MySQL ORDER BY column name
	 * @param string $sort MySQL sort statement
	 * @return array
	 */
	function getAllApprovedByGroup($gid, $order = "title", $sort = "ASC") {
		global $db;
	
		$sql = "SELECT " . $this->getSelectStatement() . "	
				FROM " . $this->getTablesStatement() . "
				WHERE images.group_id = '{$gid}'
				AND images.deleted = 0
				AND images.approved = 1
				ORDER BY images.{$order}
				{$sort}";
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Retrieve a recent number of images
	 *
	 * @param integer $limit Limit the number of results
	 * @param string $order MySQL ORDER BY column name
	 * @param string $sort MySQL sort statement
	 * @return array
	 */
	function getRecent($limit = 5, $order = 'created', $sort = 'DESC') {
		global $db;
	
		$sql = "SELECT " . $this->getSelectStatement() . "	
				FROM " . $this->getTablesStatement() . "
				WHERE images.approved = 1
				AND images.deleted = 0
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				ORDER BY images.{$order}
				{$sort}
				LIMIT 0, {$limit}";
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Sets an image to deleted status
	 *
	 * @param string $id Image's ID
	 */
	function delete($id) {
		$this->rmImage($id);
	}
	
	/**
	 * See the delete() method.
	 *
	 * @param string $id
	 */
	function rmImage($id) {
		global $db;
	
		$sql = "UPDATE " . DB_TBL_PREFIX . "gallery_images
				SET
					deleted = 1
				WHERE id = ?";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		$res = $db->execute($sth, array($id));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Retrieve all images by gallery ID number
	 *
	 * @param integer $id Gallery's ID
	 * @return array
	 */
	function getAllByGalleryId($id) {
		global $db;
	
		$sql = "SELECT " . $this->getSelectStatement() . "
				FROM ".DB_TBL_PREFIX."gallery_images_cnx AS galleries
				LEFT JOIN ".DB_TBL_PREFIX."gallery_images AS images 
				ON (images.id = galleries.image_id)
				LEFT JOIN ".DB_TBL_PREFIX."image_groups AS groups
				ON (groups.id = images.group_id)
				WHERE galleries.gallery_id = '{$id}'
				AND images.deleted = 0
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)";
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Retrieve all user's unapproved images
	 *
	 * @param string $user User's ID
	 * @return array
	 */
	function getUnapprovedByUser($user, $limit = null) {
		global $db;
	
		$sql = "SELECT " . $this->getSelectStatement() . "	
				FROM " . $this->getTablesStatement() . "
				WHERE images.approved = 0
				AND images.deleted = 0
				AND images.user_id = '{$user}'
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)";
				
		if(!is_null($limit)) {
			$sql .= " LIMIT 0,{$limit}";
		}		
		
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Retrieve all images for a user
	 * that have been approved.
	 *
	 * @param string $user
	 * @param integer $limit
	 * @return array
	 */
	function getApprovedByUser($user, $limit = null) {
		global $db;
	
		$sql = "SELECT " . $this->getSelectStatement() . "	
				FROM " . $this->getTablesStatement() . "
				WHERE images.approved = 1
				AND images.deleted = 0
				AND images.user_id = '{$user}'
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)";
				
		if(!is_null($limit)) {
			$sql .= " LIMIT 0,{$limit}";
		}		
		
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Retrieve an image's gallery info
	 *
	 * @param string $id Gallery's ID
	 * @return array
	 */
	function getGallery($id) {
		global $db;
	
		$sql = "SELECT tblB.*
				FROM ".DB_TBL_PREFIX."gallery_images_cnx AS tblA
				LEFT JOIN ".DB_TBL_PREFIX."galleries AS tblB
				ON (tblB.id = tblA.gallery_id)
				WHERE tblA.image_id = '{$id}'";
		$res = $db->getRow($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Update an image's gallery association
	 *
	 * @param string $pid Image's ID
	 * @param integer $old_gid Old gallery's ID
	 * @param integer $new_gid New gallery's ID
	 */
	function updateGallery($pid, $old_gid, $new_gid) {
		global $db;
	
		$sql = "UPDATE ".DB_TBL_PREFIX."gallery_images_cnx
				SET 
					gallery_id = ?,
					image_id = ?
				WHERE gallery_id = ?
				AND image_id = ?";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		$res = $db->execute($sth, array($new_gid, $pid, $old_gid, $pid));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Retrieve last image that was uploaded
	 * by user.
	 *
	 * @param string $uid User's ID
	 * @return array
	 */
	function getLastUserUpload($uid) {
		global $db;
	
		$sql = "SELECT " . $this->getSelectStatement() . "	
				FROM " . $this->getTablesStatement() . "
				WHERE images.approved = 0
				AND images.deleted = 0
				AND images.user_id = '{$uid}'
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				ORDER BY images.created
				LIMIT 0,1";
		$res = $db->getRow($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Used for autocomplete searching
	 * image titles by user
	 *
	 * @param string $uid User's ID
	 * @param string $str Query string
	 * @return array
	 */
	function getSearchTitlesByUser($uid, $str) {
		global $db;
	
		$sql = "SELECT title
				FROM " . DB_TBL_PREFIX . "gallery_images AS images
				WHERE user_id = '{$uid}'
				AND title LIKE '{$str}%'
				AND deleted = 0
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)";
		$res = $db->getCol($sql);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Used for autocomplete searching
	 * all images
	 *
	 * @param string $str Query string
	 * @param boolean $approved Whether to select approved or unapproved
	 * @return array
	 */
	function getSearchTitles($str, $approved = true) {
		global $db;
	
		$appr = ($approved == true) ? 1 : 0;
	
		$sql = "SELECT title
				FROM " . DB_TBL_PREFIX . "gallery_images AS images
				WHERE title LIKE '{$str}%'
				AND deleted = '0'
				AND approved = '{$appr}'
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				GROUP BY images.title";
		$res = $db->getCol($sql);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Retrieve all images by user ID
	 * and image title
	 *
	 * @param string $uid User's ID
	 * @param string $title 
	 * @return array
	 */
	function getByTitleAndUser($uid, $title) {
		global $db;
	
		$sql = "SELECT " . $this->getSelectStatement() . "	
				FROM " . $this->getTablesStatement() . "
				WHERE images.user_id = '{$uid}'
				AND images.title = '" . str_replace("+", " ", $title) . "'
				AND images.deleted = 0
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)";
		
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);

		return $res;
	}
	
	/**
	 * Retrieve all images by image title
	 *
	 * @param string $title Query string
	 * @return array
	 */
	function getAllByTitle($title, $approved = true) {
		global $db;
	
		$appr = ($approved == true) ? 1 : 0;
	
		$sql = "SELECT " . $this->getSelectStatement() . "	
				FROM " . $this->getTablesStatement() . "
				WHERE images.title LIKE '%" . str_replace("+", " ", $title) . "%'
				AND images.approved = '{$appr}'
				AND images.deleted = '0'
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)";
		
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);

		return $res;
	}
	
	/**
	 * Sets all user's images to completed
	 * upload status
	 *
	 * @param string $id User's ID
	 */
	function completeAllByUser($id) {
		global $db;
	
		$sql = "UPDATE " . DB_TBL_PREFIX . "gallery_images
				SET
					upload_complete = 1
				WHERE user_id = ?
				AND upload_complete = 0
				AND deleted = 0";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		$res = $db->execute($sth, array($id));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Sets the image to completed upload status
	 *
	 * @param string $id Image's ID
	 */
	function complete($id) {
		global $db;
	
		$sql = "UPDATE " . DB_TBL_PREFIX . "gallery_images
				SET
					upload_complete = 1
				WHERE id = ?";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		$res = $db->execute($sth, array($id));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Updates a recently uploaded image.
	 *
	 * @param string $id Image's ID
	 * @param string $title 
	 * @param integer $group Group ID
	 * @param string $loc Location where the image was created
	 */
	function updateRecent($id, $title, $group, $loc) {
		global $db;
	
		$sql = "UPDATE " . DB_TBL_PREFIX . "gallery_images
				SET
					title = ?,
					group_id = ?,
					location = ?
				WHERE id = ?";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		$res = $db->execute($sth, array(
			trim($title), 
			$group, 
			trim($loc),
			$id
		));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	
	/**
	 * Updates binary image data and updates 
	 * modified date
	 *
	 * @param string $id Image's ID
	 * @param string $data File's binary data stream
	 */
	function updateFile($id, $data) {
		global $db;
	
		$sql = "UPDATE " . DB_TBL_PREFIX . "gallery_images
				SET
					file = ?,
					modified = NOW()
				WHERE id = ?";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		$res = $db->execute($sth, array($data, $id));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	
	/**
	 * Writes EXIF data to binary image stream
	 *
	 * @param string $id Image's ID
	 * @param string $taken Date image was created
	 * @param string $make Camera make
	 * @param string $model Camera model
	 * @param string $creator
	 * @param string $copy Copyright information
	 * @param string $shutter Shutter speed
	 * @param string $aperture Aperture value
	 * @param string $iso ISO setting
	 * @param string $flash Flash setting
	 * @param string $meter Metering mode
	 * @param string $focal Focal length
	 */
	function writeExif($id, $taken, $make, $model, $creator, $copy, $shutter, $aperture, $iso, $flash, $meter, $focal) {
		$filedata = $this->getFileDataById($id);
		
		$taken = date("Y:m:d H:i:s", strtotime(trim($taken)));
		
		/**
		 * create temporary file
		 */
		$tmpfile = tempnam(APP_TMP_DIR, "exif_");
		$handle = fopen($tmpfile, "w");
		/**
		 * write database's binary data to file
		 */
		fwrite($handle, $filedata);
		fclose($handle);
		
		/**
		 * write exif to temporary file
		 */
		$cmdstr = EXIF_TOOL . " -EXIF:DateTimeOriginal=\"" . $taken . "\""
			. " -EXIF:CreateDate=\"" . $taken . "\""
			. " -EXIF:Make=\"" . $make . "\""
			. " -EXIF:Model=\"" . trim($model) . "\""
			. " -EXIF:Artist=\"" . trim($creator) . "\""
			. " -EXIF:Copyright=\"" . trim($copy) . "\""
			. " -EXIF:ShutterSpeedValue=" . trim($shutter)
			. " -EXIF:ApertureValue=" . trim($aperture)
			. " -EXIF:ISO=" . trim($iso)
			. " -EXIF:Flash=\"" . $flash ."\""
			. " -EXIF:MeteringMode=\"" . $meter . "\""
			. " -EXIF:FocalLength=" . trim($focal)
			. " " . $tmpfile;
			
		exec($cmdstr);
		
		/**
		 * open temporary file for reading
		 */
		$handle = fopen($tmpfile, "rb");
		$newfiledata = fread($handle, filesize($tmpfile));
		fclose($handle);
		
		/**
		 * update file's binary data from 
		 * temporary file to the database
		 */
		$this->updateFile($id, $newfiledata);
		
		/**
		 * delete temporary file
		 */
		unlink($tmpfile);
		
		return;
	}
	
	
	/**
	 * Sets an image to deleted status
	 * when its corresponding group is deleted
	 *
	 * @param integer $gid Group's ID
	 */
	function removeGroup($gid) {
		global $db;
	
		$sql = "UPDATE " . DB_TBL_PREFIX . "gallery_images
				SET
					deleted = ?
				WHERE group_id = ?";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		$res = $db->execute($sth, array(1, $gid));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	
	/**
	 * Updates image's map coordinates
	 *
	 * @param string $id Image's ID
	 * @param string $lat Latitude
	 * @param string $lng Longitude
	 */
	function saveCoordinates($id, $lat, $lng) {
		global $db;
	
		$sql = "UPDATE " . DB_TBL_PREFIX . "gallery_images
				SET
					lat = ?,
					lng = ?
				WHERE id = ?";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		$res = $db->execute($sth, array($lat, $lng, $id));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Retrieves recently uploaded images that are not yet approved
	 *
	 * @param string $order MySQL ORDER BY column name
	 * @param string $sort MySQL sort statement
	 * @param integer $limit Limits the number of results
	 * @return array
	 */
	function getRecentUnapproved($order = "title", $sort = "ASC", $limit = 10) {
		global $db;
		
		$sql = "SELECT " . $this->getSelectStatement() . "	
				FROM " . $this->getTablesStatement() . "
				WHERE images.approved = 0
				AND images.deleted = 0
				AND images.upload_complete = 1
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				ORDER BY images.{$order}
				{$sort}
				LIMIT 0,{$limit}";
		
		//echo $sql . "<hr />";
		
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Return a limited amount of random 
	 * approved images. Does not include 
	 * binary image data
	 * 
	 * @param integer $limit
	 * @return array
	 */
	function getRandom($limit = 5) {
		global $db;
	
		$sql = "SELECT " . $this->getSelectStatement() . "	
				FROM " . $this->getTablesStatement() . "
				WHERE images.approved = 1
				AND images.deleted = 0
				AND images.upload_complete = 1
				AND images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				ORDER BY RAND()
				LIMIT 0,{$limit}";
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Process a comma-delimited string of keywords,
	 * assigning keywords to an image
	 *
	 * @param string $img_id
	 * @param string $str Comma delimited string of keywords
	 */
	function addKeywords($img_id, $str) {
		$kwordary = explode(",", $str);
		
		$this->deleteAllKeywords($img_id);
		
		if(!empty($kwordary)) {
			foreach($kwordary as $word) {
				$curword = trim(preg_replace("#[\W]+#", " ", $word));
				
				if($curword !== "") { // do not store empty strings
					if($kid = $this->keyword->exists($curword)) {
						$this->insertKeyword($img_id, $kid);
					} else {
						$this->keyword->insert($curword);
						
						$newkword = $this->keyword->findOne($curword);
						
						$this->insertKeyword($img_id, $newkword['id']);
					}
				} // end empty string check
			}
		}
		
		return;
	}
	
	/**
	 * Deletes/flushes all image's current keywords
	 *
	 * @param string $img_id
	 */
	function deleteAllKeywords($img_id) {
		global $db;
	
		$sql = "DELETE FROM " . DB_TBL_PREFIX . "image_keywords WHERE image_id = ?";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->Message(), E_USER_ERROR);
		$res = $db->execute($sth, array($img_id));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Inserts a new keyword image relationship,
	 * if one does not already exist
	 *
	 * @param string $img_id
	 * @param integer $keyword_id
	 */
	function insertKeyword($img_id, $keyword_id) {
		global $db;
	
		$sql = "INSERT INTO " . DB_TBL_PREFIX . "image_keywords (
					image_id, keyword_id
				) VALUES (
					?, ?
				)";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->Message(), E_USER_ERROR);
		
		// don't execute query if keyword is already
		// associated with image
		if(!$this->hasKeyword($img_id, $keyword_id)) {
			$res = $db->execute($sth, array($img_id, $keyword_id));
			if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		}
		
		return;
	}
	
	/**
	 * Checks to see if an image has a keyword already
	 *
	 * @param string $img_id
	 * @param integer $keyword_id
	 * @return mixed False or ID
	 */
	function hasKeyword($img_id, $keyword_id) {
		global $db;
		
		$sql = "SELECT id
				FROM " . DB_TBL_PREFIX . "image_keywords
				WHERE image_id = '{$img_id}'
				AND keyword_id = '{$keyword_id}'";
		$res = $db->getOne($sql);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return (is_null($res)) ? false : $res;
	}
	
	/**
	 * Search database across title, 
	 * location, or keywords
	 *
	 * @param string $query
	 * @param boolean $exact
	 * @return array
	 */
	function search($query, $exact = true) {
		global $db;
	
		require_once("Gallery/Group.php");
		$group = new Gallery_Group();
	
		if(!$exact) {
			$query = preg_replace("#[\s\W]+#", "%", $query);
		}
	
		$sql = "SELECT " . $this->getSelectStatement() . "
				FROM " . DB_TBL_PREFIX . "gallery_images AS images
				LEFT JOIN ".DB_TBL_PREFIX."image_groups AS groups
				ON (groups.id= images.group_id)
				LEFT JOIN " . DB_TBL_PREFIX . "users AS users
				ON (users.id = images.user_id)
				LEFT JOIN " . DB_TBL_PREFIX . "image_keywords AS cnx1
				ON (cnx1.image_id = images.id)
				LEFT JOIN " . DB_TBL_PREFIX . "keywords AS keywords
				ON (keywords.id = cnx1.keyword_id)
				LEFT JOIN " . DB_TBL_PREFIX . "gallery_groups_cnx AS gallery_cnx ON (gallery_cnx.group_id = images.group_id)
				LEFT JOIN " . DB_TBL_PREFIX . "galleries AS gallery ON (gallery.id = gallery_cnx.gallery_id)
				LEFT JOIN " . DB_TBL_PREFIX . "galleries AS gallery2 ON (gallery.parent_id = gallery2.id)
				WHERE images.deleted = 0
				AND images.approved = 1
				AND (images.title LIKE '{$query}'
				OR images.location LIKE '{$query}'
				OR keywords.keyword LIKE '{$query}')
				GROUP BY images.id
				ORDER BY images.title
				ASC";
		
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		$ary = array();
		if(!empty($res)) {
			foreach($res as $i => $row) {
				foreach($row as $k => $v) {
					$ary[$i][$k] = $v;
				}
				
				$ary[$i]['section'] = $group->getSection($row['group_id']);
				$ary[$i]['gallery'] = $group->getGallery($row['group_id']);
			}
		}
		
		return $ary;
	}
	
	/**
	 * Search images by user
	 * first name or last name
	 *
	 * @param string $query
	 * @param string $order
	 * @param string $sort
	 * @return array
	 */
	function searchByUser($query, $order = "title", $sort = "ASC") {
		global $db;
		
		$query = preg_replace("#[\s\W]+#", "%", $query);
		
		$sql = "SELECT " . $this->getSelectStatement() . "
				FROM " . $this->getTablesStatement() . "
				WHERE images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				AND images.deleted = 0
				AND (users.firstname LIKE '%{$query}%'
				OR users.lastname LIKE '%{$query}%'
				OR CONCAT(users.firstname, ' ', users.lastname) LIKE '%{$query}%')
				ORDER BY {$order}
				{$sort}";
				
		//echo $sql . "<hr />";
				
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Search images by title only.
	 *
	 * @param string $query
	 * @param string $order
	 * @param string $sort
	 * @return array
	 */
	function searchByTitle($query, $order = "title", $sort = "ASC") {
		global $db;
		
		$query = preg_replace("#[\s\W]+#", "%", $query);
		
		$sql = "SELECT " . $this->getSelectStatement() . "
				FROM " . $this->getTablesStatement() . "
				WHERE images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				AND images.title LIKE '%{$query}%'
				AND images.deleted = 0
				ORDER BY {$order}
				{$sort}";
				
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Retrieve all images by group title search.
	 *
	 * @param string $query
	 * @param string $order
	 * @param string $sort
	 * @return array
	 */
	function searchByGroup($query, $order = "title", $sort = "ASC") {
		global $db;
		
		$query = preg_replace("#[\s\W]+#", "%", $query);
		
		$sql = "SELECT " . $this->getSelectStatement() . "
				FROM " . $this->getTablesStatement() . "
				WHERE images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				AND groups.title LIKE '%{$query}%'
				AND images.deleted = 0
				ORDER BY {$order}
				{$sort}";
				
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Retrieve all locations within a group.
	 *
	 * @param integer $group_id
	 * @return array
	 */
	function getLocationsInGroup($group_id) {
		global $db;
		
		$sql = "SELECT images.location
				FROM " . DB_TBL_PREFIX . "gallery_images AS images
				LEFT JOIN " . DB_TBL_PREFIX . "image_groups AS groups ON ( groups.id = images.group_id )
				WHERE groups.id = '{$group_id}'
				GROUP BY images.location";
				
		$res = $db->getCol($sql);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Search images by group and location.
	 *
	 * @param integer $group_id
	 * @param string $location
	 * @param string $order
	 * @param string $sort
	 * @return array
	 */
	function searchLocationsByGroup($group_id, $location, $order = "title", $sort = "ASC") {
		global $db;
		
		$location = preg_replace("#[\W\s]+#", "%", $location);
	
		$sql = "SELECT " . $this->getSelectStatement() . "	
				FROM " . $this->getTablesStatement() . "
				WHERE images.group_id = '{$group_id}'
				AND images.location LIKE '{$location}'
				AND images.deleted = 0
				AND images.approved = 1
				ORDER BY images.{$order}
				{$sort}";

		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);

		return $res;
	}
	
	/**
	 * Search images by keywords
	 *
	 * @param string $keywords
	 * @return array
	 */
	function searchKeywords($keywords) {
		require_once("Gallery/Group.php");
		$group = new Gallery_Group();
	
		global $db;
	
		$sql = "SELECT " . $this->getSelectStatement() . "
				FROM " . DB_TBL_PREFIX . "image_keywords AS cnx
				LEFT JOIN " . DB_TBL_PREFIX . "keywords AS keywords ON (keywords.id = cnx.keyword_id)
				LEFT JOIN " . DB_TBL_PREFIX . "gallery_images AS images ON (images.id = cnx.image_id)
				LEFT JOIN " . DB_TBL_PREFIX . "gallery_groups_cnx AS gallery_cnx ON (gallery_cnx.group_id = images.group_id)
				LEFT JOIN " . DB_TBL_PREFIX . "galleries AS gallery ON (gallery.id = gallery_cnx.gallery_id)
				LEFT JOIN " . DB_TBL_PREFIX . "galleries AS gallery2 ON (gallery.parent_id = gallery2.id)
				LEFT JOIN " . DB_TBL_PREFIX . "image_groups AS groups ON (groups.id = images.group_id)
				LEFT JOIN " . DB_TBL_PREFIX . "users AS users ON (users.id = images.user_id)
				WHERE images.approved = 1
				AND images.deleted = 0";
				
		if(!empty($keywords)) {
			$sql .= " AND (";
			
			foreach($keywords as $k => $v) {
				$v = preg_replace("#[\W\s]+#", "%", $v);
			
				if($k > 0) {
					$sql .= " OR keywords.keyword LIKE '{$v}'";
				} else {
					$sql .= " keywords.keyword LIKE '{$v}'";
				}
			}
			
			$sql .= ")";
		}
		
		$sql .= " GROUP BY cnx.image_id";
		
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		$ary = array();
		if(!empty($res)) {
			foreach($res as $i => $row) {
				foreach($row as $k => $v) {
					$ary[$i][$k] = $v;
				}
				
				$ary[$i]['section'] = $group->getSection($row['group_id']);
				$ary[$i]['gallery'] = $group->getGallery($row['group_id']);
			}
		}
		
		return $ary;
	}
	
	/**
	 * Retrieve all images.
	 *
	 * @param string $order
	 * @param string $sort
	 * @return array
	 */
	function getAll($order = "title", $sort = "ASC") {
		global $db;
		
		$sql = "SELECT " . $this->getSelectStatement() . "
				FROM " . $this->getTablesStatement() . "
				WHERE images.id NOT IN (SELECT image_id AS id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				ORDER BY {$order}
				{$sort}";
		
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Set status of all user's images to deleted
	 *
	 * @param string $user_id
	 */
	function deleteAllByUser($user_id) {
		global $db;
		
		$sql = "UPDATE " . DB_TBL_PREFIX . "gallery_images
				SET
					deleted = ?,
					approved = ?,
					modified = NOW()
				WHERE user_id = ?";
			
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		
		$res = $db->execute($sth, array(1, 0, $user_id));
		
		return;
	}
	
	function isPortrait($image_id) {
		global $db;
		
		$sql = "SELECT id
				FROM " . DB_TBL_PREFIX . "user_images_cnx
				WHERE image_id = '{$image_id}'";
		
		//echo $sql; die();
		
		$res = $db->getOne($sql);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		//var_dump($res); die();
		return (is_null($res)) ? false : true;
	}
}
?>