<?php
require_once './init.php';

$perPage = 4;

$groups = Ode_DBO::getInstance()->query("
	SELECT `group`.*
	FROM groups AS `group`
	WHERE `group`.is_deleted = 0
	ORDER BY `group`.title
	ASC
")->fetchAll(PDO::FETCH_CLASS, "DBO_Group_Model");

$pager = Pager::factory(array(
	'perPage' => $perPage,
	'urlVar' => "pageNum",
	'mode' => "Sliding",
	'append' => false,
	'path' => "",
	'fileName' => "javascript:groupsList(%d);",
	'delta' => 3,
	'itemData' => $groups
));

Ode_View::getInstance()->assign("groups", $pager->getPageData());
Ode_View::getInstance()->assign("pagelinks", $pager->getLinks());

header("Content-Type: text/html");
echo Ode_View::getInstance()->fetch("ajax/groupsList.tpl.php");
exit();
?>