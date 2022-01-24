<?php
if (!Ode_Auth::getInstance()->isPrivate()) {
    exit("You do not have sufficient privileges to enter this page.");
}

/**
 * Grab organizations specific to logged in user
 */
$uosql = "SELECT " . DBO_Organization::COLUMNS . "
		  FROM " . DBO_User_Organization_Cnx::TABLE_NAME . " AS b
		  LEFT JOIN " . DBO_Organization::TABLE_NAME . " AS a ON (a.id = b.org_id)
		  WHERE b.user_id = " . Ode_DBO::getInstance()->quote(Ode_Auth::getInstance()->getSession()->id) . "
		  AND a.is_deleted = 0";
/**
 * If user is admin grab all orgs
 */
if (Ode_Auth::getInstance()->isAdmin()) {
    $uosql = "SELECT " . DBO_Organization::COLUMNS . "
		  FROM " . DBO_Organization::TABLE_NAME . " AS a
		  WHERE a.is_deleted = 0";
}

$userOrgs = Ode_DBO::getInstance()->query($uosql)->fetchAll(PDO::FETCH_CLASS, DBO_Organization::MODEL_NAME);
Ode_View::getInstance()->assign("userorgs", $userOrgs);

switch (Ode_Manager::getInstance()->getMode()) {
    default:
    case false:
        switch (Ode_Manager::getInstance()->getTask()) {
            default:
            case false:
                /**
                 * Admins see all.
                 */
                if (Ode_Auth::getInstance()->isAdmin()) {
                    $sql = "SELECT " . DBO_Asset::COLUMNS . "
							FROM " . DBO_Asset::TABLE_NAME . " AS a
							WHERE a.is_deleted = 0
							ORDER BY a.modified
							DESC";
                } else { // everyone else limited to organization assignments
                    $org_ids = Util::dbQuoteListItems(DBO_User_Organization_Cnx::getUserOrgIDs(Ode_Auth::getInstance()->getSession()->id));
                    $org_ids = (!empty($org_ids)) ? implode(',', $org_ids) : "''";

                    $sql = "SELECT " . DBO_Asset::COLUMNS . "
							FROM " . DBO_Asset::TABLE_NAME . " AS a
							LEFT JOIN " . DBO_Asset_Metadata::TABLE_NAME . " AS b ON (b.asset_id = a.id)
							WHERE a.is_deleted = 0
							AND b.metadata_name = " . Ode_DBO::getInstance()->quote(DBO_Asset_Metadata::META_ORG_ID_NAME, PDO::PARAM_STR) . "
							AND b.metadata_value IN (" . $org_ids . ")
							GROUP BY a.id";
                }
                //echo $sql;

                $assets = Ode_DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_CLASS, "DBO_Asset_Model");

                Ode_View::getInstance()->assign("assets", $assets);
                break;
        }
        break;
    case 'edit':
        switch (Ode_Manager::getInstance()->getTask()) {
            default:
            case 'batch':
                $asset_ids = AssetManager::getInstance()->getEdits();
                $asset_ids = Util::dbQuoteListItems($asset_ids, PDO::PARAM_STR);

                $assets = Ode_DBO::getInstance()->query("
					SELECT " . DBO_Asset::COLUMNS . "
					FROM " . DBO_Asset::TABLE_NAME . " AS a
					WHERE a.id IN (" . implode(",", $asset_ids) . ")
				")->fetchAll(PDO::FETCH_CLASS, DBO_Asset::MODEL_NAME);

                $group = Ode_DBO::getInstance()->query("
					SELECT grp.*
					FROM groups AS grp
					WHERE grp.id = " . Ode_DBO::getInstance()->quote($_GET['id'], PDO::PARAM_STR) . "
					LIMIT 0,1
				")->fetchObject("DBO_Group_Model");

                $rights = Ode_DBO::getInstance()->query("
					SELECT " . DBO_Properties::COLUMNS . "
					FROM " . DBO_Properties::TABLE_NAME . " AS a
					WHERE a.machine_name = " . Ode_DBO::getInstance()->quote(DBO_Properties::RIGHTS_PROPERTY_NAME, PDO::PARAM_STR) . "
					AND a.is_enabled = 1
					ORDER BY a.value ASC
				")->fetchAll(PDO::FETCH_OBJ);

                Ode_View::getInstance()->assign("rights", $rights);
                Ode_View::getInstance()->assign("group", $group);
                Ode_View::getInstance()->assign("assetids", implode(",", AssetManager::getInstance()->getEdits()));

                Ode_View::getInstance()->assign("assets", $assets);
                break;
        }
        break;
    case 'search':
        switch (Ode_Manager::getInstance()->getTask()) {
            default:
            case false:
                $org_ids = Util::dbQuoteListItems(DBO_User_Organization_Cnx::getUserOrgIDs(Ode_Auth::getInstance()->getSession()->id));
                $org_ids = (!empty($org_ids)) ? implode(',', $org_ids) : "''";

                $plainQry = trim($_GET['q']);
                $qry = "%" . preg_replace("/[\s\r\n\t\W]+/", "%", $plainQry) . "%";

                if (Ode_Auth::getInstance()->isAdmin()) { // admins get all
                    $sql = "SELECT " . DBO_Asset::COLUMNS . "
							FROM " . DBO_Asset::TABLE_NAME . " AS a
							LEFT JOIN keyword_asset_cnx AS ka_cnx ON (ka_cnx.asset_id = a.id)
							LEFT JOIN keywords AS keyword ON (keyword.id = ka_cnx.keyword_id)
							LEFT JOIN captions AS caption ON (caption.asset_id = a.id)
							WHERE a.is_deleted = 0
							AND (
								a.title LIKE " . Ode_DBO::getInstance()->quote($qry, PDO::PARAM_STR) . "
								OR a.description LIKE " . Ode_DBO::getInstance()->quote($qry, PDO::PARAM_STR) . "
								OR keyword.keyword LIKE " . Ode_DBO::getInstance()->quote($qry, PDO::PARAM_STR) . "
								OR caption.caption LIKE " . Ode_DBO::getInstance()->quote($qry, PDO::PARAM_STR) . "
							)
							GROUP BY a.id
							ORDER BY a.title
							ASC";
                } else { // everyone else is limited by their organization
                    $sql = "SELECT " . DBO_Asset::COLUMNS . "
							FROM " . DBO_Asset::TABLE_NAME . " AS a
							LEFT JOIN keyword_asset_cnx AS ka_cnx ON (ka_cnx.asset_id = a.id)
							LEFT JOIN keywords AS keyword ON (keyword.id = ka_cnx.keyword_id)
							LEFT JOIN captions AS caption ON (caption.asset_id = a.id)
							LEFT JOIN " . DBO_Asset_Metadata::TABLE_NAME . " AS meta ON (meta.asset_id = a.id)
							WHERE a.is_deleted = 0
							AND (
								a.title LIKE " . Ode_DBO::getInstance()->quote($qry, PDO::PARAM_STR) . "
								OR a.description LIKE " . Ode_DBO::getInstance()->quote($qry, PDO::PARAM_STR) . "
								OR keyword.keyword LIKE " . Ode_DBO::getInstance()->quote($qry, PDO::PARAM_STR) . "
								OR caption.caption LIKE " . Ode_DBO::getInstance()->quote($qry, PDO::PARAM_STR) . "
							)
							AND meta.metadata_name = " . Ode_DBO::getInstance()->quote(DBO_Asset_Metadata::META_ORG_ID_NAME, PDO::PARAM_STR) . "
							AND meta.metadata_value IN (" . $org_ids . ")
							GROUP BY a.id
							ORDER BY a.title
							ASC";
                }
                //echo $sql;

                $assets = Ode_DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_CLASS, "DBO_Asset_Model");
                //$assets = Ode_DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_OBJ);

                $pager = Pager::factory(array(
                    'perPage' => 10,
                    'mode' => "Jumping",
                    'delta' => 4,
                    'itemData' => $assets
                ));

                Ode_View::getInstance()->assign("query", $plainQry);
                Ode_View::getInstance()->assign("assets", $pager->getPageData());
                Ode_View::getInstance()->assign("assetlinks", $pager->getLinks());
                Ode_View::getInstance()->assign("pageid", $pageId);
                Ode_View::getInstance()->assign("num_pages", $pager->numPages());
                Ode_View::getInstance()->assign("num_items", count($assets));
                break;
        }
        break;
    case 'by':
        switch (Ode_Manager::getInstance()->getTask()) {
            default:

                break;
            case 'org':
                $org_id = trim($_GET['org_id']);
                if (empty($org_id)) {
                    header("Location: " . Ode_Manager::getInstance()->action("admin_assets"));
                    exit();
                }

                if (Ode_Auth::getInstance()->isAdmin()) {
                    $sql = "SELECT " . DBO_Asset::COLUMNS . "
							FROM " . DBO_Asset_Metadata::TABLE_NAME . " AS b
							LEFT JOIN " . DBO_Asset::TABLE_NAME . " AS a ON (a.id = b.asset_id)
							WHERE b.metadata_name = " . Ode_DBO::getInstance()->quote(DBO_Asset_Metadata::META_ORG_ID_NAME, PDO::PARAM_STR) . "
							AND b.metadata_value = " . Ode_DBO::getInstance()->quote($org_id, PDO::PARAM_INT) . "
							AND a.is_deleted = 0
							ORDER BY a.modified
							DESC";
                } else {
                    $sql = "SELECT " . DBO_Asset::COLUMNS . "
							FROM " . DBO_Asset_Metadata::TABLE_NAME . " AS b
							LEFT JOIN " . DBO_Asset::TABLE_NAME . " AS a ON (a.id = b.asset_id)
							LEFT JOIN " . DBO_User_Organization_Cnx::TABLE_NAME . " AS c ON (c.org_id = b.metadata_value)
							WHERE b.metadata_name = " . Ode_DBO::getInstance()->quote(DBO_Asset_Metadata::META_ORG_ID_NAME, PDO::PARAM_STR) . "
							AND b.metadata_value = " . Ode_DBO::getInstance()->quote($org_id, PDO::PARAM_INT) . "
							AND c.user_id = " . Ode_DBO::getInstance()->quote(Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR) . "
							AND a.is_deleted = 0
							ORDER BY a.modified
							DESC";
                }

                //echo $sql; die();

                $assets = Ode_DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_CLASS, "DBO_Asset_Model");

                $pager = Pager::factory(array(
                    'perPage' => 10,
                    'mode' => "Jumping",
                    'delta' => 4,
                    'itemData' => $assets
                ));

                Ode_View::getInstance()->assign("assets", $pager->getPageData());
                Ode_View::getInstance()->assign("assetlinks", $pager->getLinks());
                Ode_View::getInstance()->assign("pageid", $pageId);
                Ode_View::getInstance()->assign("num_pages", $pager->numPages());
                Ode_View::getInstance()->assign("num_items", count($assets));
                Ode_View::getInstance()->assign("curorgid", $org_id);
                break;
        }
        break;
}
?>