<?php
require_once 'init.php';

/**
 * only admins have access to groups globally
 */
if(Ode_Auth::getInstance()->isAdmin()) {
	$sql = "SELECT " . DBO_Group::COLUMNS . "
			FROM " . DBO_Group::TABLE_NAME . " AS a
			WHERE a.title LIKE " . Ode_DBO::getInstance()->quote($_POST['query'] . "%", PDO::PARAM_STR) . "
			AND a.is_deleted = 0
			AND a.id NOT IN (
				SELECT group_id 
				FROM asset_group_cnx 
				WHERE asset_id = " . Ode_DBO::getInstance()->quote($_POST['_aid'], PDO::PARAM_STR) . "
			)
			ORDER BY a.title
			ASC
			LIMIT 0,20";
} else {
	$org_ids = Util::dbQuoteListItems(DBO_User_Organization_Cnx::getUserOrgIDs(Ode_Auth::getInstance()->getSession()->id));
	$org_ids = (!empty($org_ids)) ? implode(',', $org_ids) : "''";
	
	$sql = "SELECT " . DBO_Group::COLUMNS . "
			FROM " . DBO_Group::TABLE_NAME . " AS a
			LEFT JOIN " . DBO_Group_Metadata::TABLE_NAME . " AS b ON (b.group_id = a.id)
			WHERE a.title LIKE " . Ode_DBO::getInstance()->quote($_POST['query'] . "%", PDO::PARAM_STR) . "
			AND a.is_deleted = 0
			AND a.id NOT IN (
				SELECT group_id 
				FROM asset_group_cnx 
				WHERE asset_id = " . Ode_DBO::getInstance()->quote($_POST['_aid'], PDO::PARAM_STR) . "
			)
			AND b.meta_name = " . Ode_DBO::getInstance()->quote(DBO_Group_Metadata::META_ORG_ID_NAME, PDO::PARAM_STR) . "
			AND b.meta_value IN (" . $org_ids . ")
			ORDER BY a.title
			ASC
			LIMIT 0,20";
}

$groups = Ode_DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$json = new Services_JSON();
header("Content-Type: application/json");
echo $json->encode($groups);
exit();
?>