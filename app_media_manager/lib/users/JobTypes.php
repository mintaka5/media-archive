<?php
/**
 * @author C.J. Walsh <cj@perigeeglobal.com>
 * @copyright Copyright (c) 2008, C.J. Walsh
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://www.perigeeglobal.com
 * @package Users
 * 
 */
class Users_JobTypes {
	/**
	 * Constructor
	 *
	 * @return Users_JobTypes
	 */
	function Users_JobTypes() {}
	
	/**
	 * Retrieve all job titles.
	 *
	 * @param string $order
	 * @param string $sort
	 * @param integer $limit
	 * @return array
	 */
	function getAll($order = "title", $sort = "ASC", $limit = null) {
		global $db;
		
		$sql = "SELECT *
				FROM " . DB_TBL_PREFIX . "job_titles
				WHERE deleted = 0
				ORDER BY {$order}
				{$sort}";
		
		if(!is_null($limit)) {
			$sql .= " LIMIT 0,{$limit}";
		}
		
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
}
?>