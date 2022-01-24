<?php
require_once './init.php';

$sql = "
	SELECT `group`.*
	FROM asset_group_cnx AS cnx
	LEFT JOIN groups AS `group` ON (`group`.id = cnx.group_id)
	WHERE cnx.asset_id = " . Ode_DBO::getInstance()->quote($_POST['id'], PDO::PARAM_STR) . "
	AND `group`.is_deleted = 0
	ORDER BY `group`.title
	ASC
";

$groups = Ode_DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_CLASS, "DBO_Group_Model");

Ode_View::getInstance()->assign("groups", $groups);
header("Content-Type: text/html");
echo Ode_View::getInstance()->fetch("ajax/assetGroupsList.tpl.php");
exit();
?>