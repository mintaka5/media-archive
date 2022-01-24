<?php
require_once './init.php';

Ode_DBO::getInstance()->setAttribute(PDO::ATTR_PERSISTENT, true);

$assets = Ode_DBO::getInstance()->query("
	SELECT " . DBO_Asset_Group_Cnx::SELECT_COLUMNS . "
	FROM " . DBO_Asset_Group_Cnx::TABLE_NAME . " AS a
	LEFT JOIN assets AS ast ON (ast.id = a.asset_id)
	WHERE a.group_id = " . Ode_DBO::getInstance()->quote($_POST['_gid'], PDO::PARAM_STR) . "
	AND ast.is_deleted = 0
")->fetchAll(PDO::FETCH_CLASS, "DBO_Asset_Group_Cnx_Model");

Ode_View::getInstance()->assign("assets", $assets);

Ode_DBO::getInstance()->setAttribute(PDO::ATTR_PERSISTENT, false);

header("Content-Type: text/html");
echo Ode_View::getInstance()->fetch("ajax/groupAssetsList.tpl.php");
exit();
?>