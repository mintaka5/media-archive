<?php
require_once './init.php';

$relationship = Ode_DBO::getInstance()->query("
	SELECT " . DBO_Asset_Group_Cnx::SELECT_COLUMNS . "
	FROM " . DBO_Asset_Group_Cnx::TABLE_NAME . " AS a
	LEFT JOIN groups AS `group` ON (`group`.id = a.group_id)
	LEFT JOIN assets AS asset ON (asset.id = a.asset_id)
	WHERE a.asset_id = " . Ode_DBO::getInstance()->quote($_POST['aid'], PDO::PARAM_STR) . "
	AND asset.is_deleted = 0
	AND `group`.is_deleted = 0
	AND a.group_id = " . Ode_DBO::getInstance()->quote($_POST['gid'], PDO::PARAM_STR) . "
	LIMIT 0,1
")->fetch(PDO::FETCH_ASSOC);

/**
 * If relationship does not already exist
 * then create it!
 */
if($relationship == false) {
	$sth = Ode_DBO::getInstance()->prepare("INSERT INTO " . DBO_Asset_Group_Cnx::TABLE_NAME . " (asset_id, group_id) VALUES (:asset_id, :group_id);");
	$sth->bindValue(":asset_id", $_POST['aid'], PDO::PARAM_STR);
	$sth->bindValue(":group_id", $_POST['gid'], PDO::PARAM_STR);
	
	try {
		$sth->execute();
		
		$cnxId = Ode_DBO::getInstance()->query("
			SELECT a.id 
			FROM " . DBO_Asset_Group_Cnx::TABLE_NAME . " AS a
			WHERE a.asset_id = " . Ode_DBO::getInstance()->quote($_POST['aid'], PDO::PARAM_STR) . "
			AND a.group_id = " . Ode_DBO::getInstance()->quote($_POST['gid'], PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchColumn();
		
		$sth = Ode_DBO::getInstance()->prepare("
			INSERT IGNORE INTO asset_group_def (cnx_id)
			VALUES (:cnx_id)
		");
		$sth->bindValue(":cnx_id", $cnxId, PDO::PARAM_INT);
		
		try {
			$sth->execute();
		} catch (PDOException $e) {
			//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
            error_log($e->getMessage(), 0);
			
			Util::json(false);
		}
	} catch (PDOException $e) {
		//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
        error_log($e->getMessage(), 0);
		
		Util::json(false);
	}
	
	Util::json($_POST);
	
} else {
	Util::json($relationship);
}
exit();
?>