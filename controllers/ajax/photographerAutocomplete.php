<?php
require_once './init.php';

switch(Ode_Manager::getInstance()->getMode()) {
	default:
		$qry = preg_replace("/[\s\t\r\n\W]+/", "%", trim($_POST['query'])) . "%";
		
		$photogs = Ode_DBO::getInstance()->query("
			SELECT " . DBO_Photographer::SELECT_COLUMNS . "
			FROM " . DBO_Photographer::TABLE_NAME . " AS a
			WHERE a.firstname LIKE " . Ode_DBO::getInstance()->quote($qry, PDO::PARAM_STR) . "
			OR a.lastname LIKE " . Ode_DBO::getInstance()->quote($qry, PDO::PARAM_STR) . "
			ORDER BY a.lastname
			ASC
		")->fetchAll(PDO::FETCH_ASSOC);
		
		Util::json($photogs);
		exit();
		break;
}
?>