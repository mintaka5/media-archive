<?php
require_once './init.php';

switch(Ode_Manager::getInstance()->getMode()) {
	default:
		$qry = '%' . preg_replace("/[\s\t\n\r\W]/", "%", trim($_POST['query'])) . '%';
		
		$shoots = Ode_DBO::getInstance()->query("
			SELECT
				a.id, a.title,
				DATE_FORMAT(a.shoot_date, '%b %e, %Y') AS shoot_date
			FROM shoots AS a
			WHERE a.title LIKE " . Ode_DBO::getInstance()->quote($qry, PDO::PARAM_STR) . "
			ORDER BY a.title
			ASC
		")->fetchAll(PDO::FETCH_ASSOC);
		
		Util::json($shoots);
		exit();
		break;
}
?>