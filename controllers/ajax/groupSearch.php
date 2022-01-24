<?php
require_once './init.php';

switch (Ode_Manager::getInstance()->getMode()) {
	default:
		$qry = "%" . preg_replace("/[\s\t\r\n\W]+/", "%", trim($_POST['qry'])) . "%";
		
		$sql = "SELECT " . DBO_Group::COLUMNS . "
				FROM " . DBO_Group::TABLE_NAME . " AS a
				WHERE title LIKE " . Ode_DBO::getInstance()->quote($qry, PDO::PARAM_STR) . "
				AND a.is_deleted = 0
				ORDER BY a.title
				ASC";
		
		$grps = Ode_DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_CLASS, DBO_Group::MODEL_NAME);
		
		$pager = Pager::factory(array(
			'perPage' => 5,
			'urlVar' => "pageNum",
			'mode' => "Sliding",
			'append' => false,
			'path' => "",
			'fileName' => "javascript:groupsList(%d);",
			'delta' => 3,
			'itemData' => $grps
		));
		
		Ode_View::getInstance()->assign("groups", $pager->getPageData());
		Ode_View::getInstance()->assign("pagelinks", $pager->getLinks());
		
		echo Ode_View::getInstance()->fetch("ajax/groupsList.tpl.php");
		exit();
		break;
}
?>