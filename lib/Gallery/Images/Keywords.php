<?php
/**
 * Manages image information, including
 * database interaction, adn image manipulation.
 *
 * @author C.J. Walsh <cj@perigeeglobal.com>
 * @copyright Copyright (c) 2008, C.J. Walsh
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://www.perigeeglobal.com
 * @package Gallery_Images
 */
class Gallery_Images_Keywords {
	/**
	 * Constructor
	 *
	 * @return Gallery_Images_Keywords
	 */
	function Gallery_Images_Keywords() {}
	
	/**
	 * Retrieve the keywords for an image
	 *
	 * @param string $image_id
	 * @return array
	 */
	function getAllByImage($image_id) {
		global $db;
	
		$sql = "SELECT
					keywords.keyword
				FROM " . DB_TBL_PREFIX . "image_keywords AS cnx
				LEFT JOIN " . DB_TBL_PREFIX . "keywords AS keywords
				ON (keywords.id = cnx.keyword_id)
				WHERE cnx.image_id = '{$image_id}'";
		$res = $db->getCol($sql);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Search for keywords by keyword. 
	 * This query has a wildcard on the end.
	 *
	 * @param string $str Query string
	 * @return array
	 */
	function search($str) {
		global $db;
	
		$sql = "SELECT *
				FROM " . DB_TBL_PREFIX . "keywords
				WHERE keyword LIKE '{$str}%'
				ORDER BY keyword
				ASC";
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Search for a single keyword
	 *
	 * @param string $str Query string
	 * @return array
	 */
	function findOne($str) {
		global $db;
	
		$sql = "SELECT *
				FROM " . DB_TBL_PREFIX . "keywords
				WHERE keyword = '{$str}'";
		$res = $db->getRow($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Checks to see if a keyword already exists,
	 * avoiding duplicates.
	 *
	 * @param string $str Query string
	 * @return mixed False or ID
	 */
	function exists($str) {
		global $db;
	
		$sql = "SELECT id
				FROM " . DB_TBL_PREFIX . "keywords
				WHERE keyword = '{$str}'";
		$res = $db->getOne($sql);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return (is_null($res)) ? false : $res;
	}
	
	/**
	 * Inserts a new keyword
	 *
	 * @param string $keyword
	 */
	function insert($keyword) {
		global $db;
	
		$sql = "INSERT INTO " . DB_TBL_PREFIX . "keywords (keyword) VALUES (?)";
		$sth = $db->prepare($sql);
		if(DB::isError($sth)) trigger_error($sth->getMessage(), E_USER_ERROR);
		$res = $db->execute($sth, array(strtolower($keyword)));
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return;
	}
}
?>