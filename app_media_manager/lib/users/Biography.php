<?php
/**
 * @author C.J. Walsh <cj@perigeeglobal.com>
 * @copyright Copyright (c) 2008, C.J. Walsh
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://www.perigeeglobal.com
 * @package Users
 * 
 */
class Users_Biography {
	/**
	 * SELECT statement constructor
	 *
	 * @var string 
	 */
	var $_selects = "
		users.id AS id,
		users.email AS email,
		users.firstname AS firstname,
		users.lastname AS lastname,
		CONCAT(users.firstname, ' ', users.lastname) AS fullname,
		users.created AS created,
		bio.id AS bio_id,
		bio.content AS bio_content,
		bio.website AS website,
		bio.created AS bio_created,
		portrait.image_id AS portrait_id
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
	 * @return Users_Biography
	 */
	function Users_Biography() {
		$this->_tables = DB_TBL_PREFIX . "user_bios AS bio
						 LEFT JOIN " . DB_TBL_PREFIX . "users AS users ON (users.id = bio.user_id)
						 LEFT JOIN " . DB_TBL_PREFIX . "user_images_cnx AS portrait ON (portrait.user_id = bio.user_id)";
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
	 * Retrieve one user with a biography
	 *
	 * @param string $user_id
	 * @return array
	 */
	function getOneByUser($user_id) {
		global $db;
		
		$sql = "SELECT " . $this->getSelects() . "
				FROM " . $this->getTables() . "
				WHERE bio.user_id = '{$user_id}'";
				
		$res = $db->getRow($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Retrieve all users with a biography
	 * that are not featured.
	 *
	 * @param string $order
	 * @param string $sort
	 * @param integer $deleted
	 * @return array
	 */
	function getAllNotFeatured($order = "lastname", $sort = "ASC", $deleted = 0) {
		global $db;
		
		$sql = "SELECT " . $this->getSelects() . "
				FROM " . $this->getTables() . "
				WHERE users.deleted = '{$deleted}'
				AND users.approved > 0
				AND users.id NOT IN (SELECT user_id FROM " . DB_TBL_PREFIX . "users_featured)
				ORDER BY users.{$order}
				{$sort}";
				
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Retrieve all users with a biography.
	 *
	 * @param string $order
	 * @param string $sort
	 * @param integer $deleted
	 * @return array
	 */
	function getAll($order = "lastname", $sort = "ASC", $deleted = 0) {
		global $db;
		
		$sql = "SELECT " . $this->getSelects() . "
				FROM " . $this->getTables() . "
				WHERE users.deleted = '{$deleted}'
				AND users.approved > 0
				AND users.id IN (SELECT user_id FROM " . DB_TBL_PREFIX . "user_images_cnx)
				ORDER BY users.{$order}
				{$sort}";
				
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * User auto-complete first and 
	 * lastname search
	 *
	 * @param string $query
	 * @return array
	 */
	function autocomplete_search($query) {
		global $db;
		
		$query = preg_replace("#[\W\d\s]+#", "%", $query);
		
		$sql = "SELECT " . $this->getSelects() . "
				FROM " . $this->getTables() . "
				WHERE (lastname LIKE '%{$query}%'
				OR firstname LIKE '%{$query}%')";
				
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Checks to see if user has a biography.
	 *
	 * @param string $user_id
	 * @return boolean
	 */
	function hasBio($user_id) {
		global $db;
		
		$sql = "SELECT id
				FROM " . DB_TBL_PREFIX . "user_bios
				WHERE user_id = '{$user_id}'";
				
		$res = $db->getOne($sql);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return (!is_null($res)) ? $res : false;
	}
	
	/**
	 * Insert a new user biography.
	 *
	 * @param string $user_id
	 * @param string $content
	 * @param string $url
	 */
	function insert($user_id, $content, $url) {
		global $db;
		
		$sql = "INSERT INTO " . DB_TBL_PREFIX . "user_bios (
					id, user_id, content, website,
					created, modified
				) VALUES (
					UUID(), ?, ?, ?,
					NOW(), NOW()
				)";
				
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getDebugInfo(), E_USER_ERROR);
		
		$res = $db->execute($sth, array(
			$user_id,
			trim($content),
			trim($url)
		));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
	
	/**
	 * Uppdate user's biographical information.
	 *
	 * @param string $bio_id
	 * @param string $content
	 * @param string $url
	 */
	function update($bio_id, $content, $url) {
		global $db;
		
		$sql = "UPDATE " . DB_TBL_PREFIX . "user_bios
				SET
					content = ?,
					website = ?,
					modified = NOW()
				WHERE id = ?";
				
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getDebugInfo(), E_USER_ERROR);
		
		$res = $db->execute($sth, array(
			trim($content),
			trim($url),
			$bio_id
		));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
}
?>