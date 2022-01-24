<?php
require_once './init.php';

$term = "%" . trim($_POST['term']) . "%";

/**
 * only admins have access to groups globally
 */
if(Ode_Auth::getInstance()->isAdmin()) {
	$sql = "SELECT assets.*
			FROM assets AS assets
			LEFT JOIN keyword_asset_cnx AS kac ON (kac.asset_id = assets.id)
			LEFT JOIN keywords ON (keywords.id = kac.keyword_id)
			WHERE assets.is_deleted = 0
			AND (
				assets.title LIKE " . Ode_DBO::getInstance()->quote($term, PDO::PARAM_STR) . "
				OR keywords.keyword LIKE " . Ode_DBO::getInstance()->quote($term, PDO::PARAM_STR) . "
			)
			AND assets.id NOT IN (
				SELECT asset_id
				FROM asset_group_cnx
				WHERE group_id = " . Ode_DBO::getInstance()->quote($_POST['_gid'], PDO::PARAM_STR) . "
			)
			GROUP BY assets.id";
} else {
	$org_ids = Util::dbQuoteListItems(DBO_User_Organization_Cnx::getUserOrgIDs(Ode_Auth::getInstance()->getSession()->id));
	$org_ids = (!empty($org_ids)) ? implode(',', $org_ids) : "''";
	
	$sql = "SELECT " . DBO_Asset::COLUMNS . "
			FROM " . DBO_Asset::TABLE_NAME . " AS a
			LEFT JOIN keyword_asset_cnx AS kac ON (kac.asset_id = a.id)
			LEFT JOIN keywords ON (keywords.id = kac.keyword_id)
			LEFT JOIN " . DBO_Asset_Metadata::TABLE_NAME . " AS b ON (b.asset_id = a.id)
			WHERE a.is_deleted = 0
			AND b.metadata_name = " . Ode_DBO::getInstance()->quote(DBO_Asset_Metadata::META_ORG_ID_NAME, PDO::PARAM_STR) . "
			AND b.metadata_value IN (" . $org_ids . ")
			AND (
				a.title LIKE " . Ode_DBO::getInstance()->quote($term, PDO::PARAM_STR) . "
				OR keywords.keyword LIKE " . Ode_DBO::getInstance()->quote($term, PDO::PARAM_STR) . "
			)
			AND a.id NOT IN (
				SELECT asset_id
				FROM asset_group_cnx
				WHERE group_id = " . Ode_DBO::getInstance()->quote($_POST['_gid'], PDO::PARAM_STR) . "
			)
			GROUP BY a.id";
}
//echo $sql;

$assets = Ode_DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_CLASS, "DBO_Asset_Model");

Ode_View::getInstance()->assign("assets", $assets);

header("Content-Type: text/html");
echo Ode_View::getInstance()->fetch("ajax/groupAssetSearch.tpl.php");
exit();
?>