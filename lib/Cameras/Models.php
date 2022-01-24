<?php
/**
 * Database interface for camera models.
 *
 * @author C.J. Walsh <cj@perigeeglobal.com>
 * @copyright Copyright (c) 2008, C.J. Walsh
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://www.perigeeglobal.com
 * @package Cameras
 */
class Cameras_Models {
	/**
	 * Constructor
	 *
	 * @return Cameras_Models
	 */
	function Cameras_Models() {}
	
	/**
	 * Retrieve all models for a specified make
	 *
	 * @param integer $make_id
	 * @param string $order MySQL ORDER BY column name
	 * @param string $sort MySQL sort statement
	 * @return array
	 */
	function getAllByMake($make_id, $order = "model", $sort = "ASC") {
		global $db;
		
		$sql = "SELECT
					makes.make
					models.*
				FROM " . DB_TBL_PREFIX . "camera_models AS models
				LEFT JOIN " . DB_TBL_PREFIX . "camera_makes AS makes
				ON (makes.id = models.make_id)
				WHERE models.make_id = '{$make_id}'
				ORDER BY models.{$order}
				{$sort}";
		
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
}
?>