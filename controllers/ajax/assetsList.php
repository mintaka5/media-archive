<?php
require_once './init.php';

Ode_DBO::getInstance()->setAttribute(PDO::ATTR_PERSISTENT, true);

if($_GET['qry'] == "") {
	$sql = "SELECT asset.*
			FROM assets AS asset
			WHERE asset.is_deleted = 0
			ORDER BY asset.modified
			DESC";
} else {
	$plainQry = trim($_REQUEST['qry']);
	$qry = "%" . preg_replace("[\s\r\n\t]+", "%", $plainQry) . "%";
	
	$sql = "SELECT asset.*
			FROM assets AS asset
			LEFT JOIN keyword_asset_cnx AS ka_cnx ON (ka_cnx.asset_id = asset.id)
			LEFT JOIN keywords AS keyword ON (keyword.id = ka_cnx.keyword_id)
			LEFT JOIN captions AS caption ON (caption.asset_id = asset.id)
			WHERE asset.is_deleted = 0
			AND (
				asset.title LIKE " . Ode_DBO::getInstance()->quote($qry, PDO::PARAM_STR) . "
				OR asset.description LIKE " . Ode_DBO::getInstance()->quote($qry, PDO::PARAM_STR) . "
				OR keyword.keyword LIKE " . Ode_DBO::getInstance()->quote($qry, PDO::PARAM_STR) . "
				OR caption.caption LIKE " . Ode_DBO::getInstance()->quote($qry, PDO::PARAM_STR) . "
			)
			GROUP BY asset.id
			ORDER BY asset.title
			ASC";
}

$assets = Ode_DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_CLASS, "DBO_Asset_Model");

$pager = Pager::factory(array(
	'perPage' => 12,
	'urlVar' => "pageNum",
	'mode' => "Sliding",
	'append' => false,
	'path' => "",
	'fileName' => "javascript:assetsList(%d);",
	'delta' => 4,
	'itemData' => $assets
));

Ode_View::getInstance()->assign("assets", $pager->getPageData());
Ode_View::getInstance()->assign("assetlinks", $pager->getLinks());

Ode_DBO::getInstance()->setAttribute(PDO::ATTR_PERSISTENT, false);

header("Content-Type: text/html");
echo Ode_View::getInstance()->fetch("ajax/assetsList.tpl.php");
exit();
?>