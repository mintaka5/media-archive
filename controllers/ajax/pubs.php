<?php
require_once './init.php';

switch(Ode_Manager::getInstance()->getMode()) {
	default:break;
	case 'search':
		$query = "%" . preg_replace("/[\s\t\n\r\W]+/", "%", trim($_POST['query'])) . "%";
		
		$pubs = Ode_DBO::getInstance()->query("
			SELECT " . DBO_Publication::COLUMNS . "
			FROM " . DBO_Publication::TABLE_NAME . " AS a
			WHERE a.title LIKE " . Ode_DBO::getInstance()->quote($query, PDO::PARAM_STR) . "
		")->fetchAll(PDO::FETCH_ASSOC);
		
		Util::json($pubs);
		exit();
		break;
	case 'list':
		switch(Ode_Manager::getInstance()->getTask()) {
			default:
				$pubs = DBO_Publication::getAllActive();
				
				Ode_View::getInstance()->assign("pubs", $pubs);
				
				echo Ode_View::getInstance()->fetch("ajax/pubs/admin_list.tpl.php");
				exit();
				break;
		}
		break;
}
?>