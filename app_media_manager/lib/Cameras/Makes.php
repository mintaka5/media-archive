<?php
/**
 * Database interface for camera makes.
 *
 * @author C.J. Walsh <cj@perigeeglobal.com>
 * @copyright Copyright (c) 2008, C.J. Walsh
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://www.perigeeglobal.com
 * @package Cameras
 */
class Cameras_Makes {
	/**
	 * Constructor
	 *
	 * @return Cameras_Makes
	 */
	function Cameras_Makes() {}
	
	/**
	 * Retrieve all makes
	 *
	 * @param string $order MySQL ORDER BY column name
	 * @param string $sort MySQL sort statement
	 * @return array
	 */
	function getAll($order = "make", $sort = "ASC") {
		global $db;
		
		$sql = "SELECT *
				FROM " . DB_TBL_PREFIX . "camera_makes AS makes
				ORDER BY makes.{$order}
				{$sort}";
		
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_ALL);
		
		return $res;
	}
}
?>