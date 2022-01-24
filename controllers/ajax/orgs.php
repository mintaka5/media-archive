<?php

require_once './init.php';

switch (Ode_Manager::getInstance()->getMode()) {
    default:
        switch (Ode_Manager::getInstance()->getTask()) {
            default:
                $orgs = array();
                
                if(Ode_Auth::getInstance()->isAdmin()) {
                    $orgs = Ode_DBO::getInstance()->query("
                            SELECT " . DBO_Organization::COLUMNS . "
                            FROM " . DBO_Organization::TABLE_NAME . " AS a
                            WHERE a.is_deleted = 0
                            ORDER BY a.title
                            ASC
                    ")->fetchAll(PDO::FETCH_CLASS, DBO_Organization::MODEL_NAME);
                }
                
                if(Ode_Auth::getInstance()->isManager()) {
                    $org_ids = Util::dbQuoteListItems(DBO_User_Organization_Cnx::getUserOrgIDs(Ode_Auth::getInstance()->getSession()->id));
                    $org_ids = (!empty($org_ids)) ? implode(',', $org_ids) : "''";
                    
                    $orgs = Ode_DBO::getInstance()->query("
                        SELECT " . DBO_Organization::COLUMNS . "
                        FROM " . DBO_Organization::TABLE_NAME . " AS a
                        WHERE a.is_deleted = 0
                        AND a.id IN (" . $org_ids . ")
                        ORDER BY a.title
                        ASC
                    ")->fetchAll(PDO::FETCH_CLASS, DBO_Organization::MODEL_NAME);
                }

                $pager = Pager::factory(array(
                            'perPage' => 25,
                            'urlVar' => "pageNum",
                            'mode' => "Sliding",
                            'append' => false,
                            'path' => "",
                            'fileName' => "javascript:listOrgs(%d);",
                            'delta' => 4,
                            'itemData' => $orgs
                ));

                Ode_View::getInstance()->assign("orgs", $pager->getPageData());
                Ode_View::getInstance()->assign("pagelinks", $pager->getLinks());

                header('Content-Type: text/html');
                echo Ode_View::getInstance()->fetch("ajax/orgs/admin_list.tpl.php");
                exit();
                break;
        }
        break;

    case 'del':
        switch (Ode_Manager::getInstance()->getTask()) {
            default:
                $org_id = $_POST['org_id'];

                $del = DBO_Organization::delete($org_id);

                Util::json($_POST);
                exit();
                break;
        }
        break;
    case 'add':
        switch (Ode_Manager::getInstance()->getTask()) {
            default:
                $title = trim($_POST['title']);
                $org_name = DBO_Organization::generateName($title);

                $sth = Ode_DBO::getInstance()->prepare("
					INSERT INTO " . DBO_Organization::TABLE_NAME . " (org_name, title, is_deleted, created)
					VALUES (:org_name, :title, 0, NOW())
				");
                $sth->bindParam(":org_name", $org_name, PDO::PARAM_STR, 45);
                $sth->bindParam(":title", $title, PDO::PARAM_STR, 45);

                try {
                    $sth->execute();
                } catch (PDOException $e) {
                    error_log($e->getMessage(), 0);
                } catch (Exception $e) {
                    error_log($e->getMessage(), 0);
                }

                Util::json($_POST);
                exit();
                break;
        }
        break;
    case 'users':
        switch (Ode_Manager::getInstance()->getTask()) {
            default:
                $org_id = $_GET['org_id'];
                $org = DBO_Organization::getOneById($org_id);

                Ode_View::getInstance()->assign("org", $org);

                echo Ode_View::getInstance()->fetch("ajax/orgs/user_list.tpl.php");
                exit();
                break;
            case 'list':
                $org_id = $_GET['org_id'];
                $org = DBO_Organization::getOneById($org_id);

                Ode_View::getInstance()->assign("org", $org);

                echo Ode_View::getInstance()->fetch("ajax/orgs/org_users_list.tpl.php");
                exit();
                break;
            case 'rmv':
                $org_id = $_POST['org_id'];
                $user_id = $_POST['user_id'];

                DBO_User_Organization_Cnx::removeUserFromOrg($user_id, $org_id);

                Util::json($_POST);
                exit();
                break;
            case 'find':
                $org_id = $_POST['org_id'];
                $terms = preg_replace("/[\s\t\n\r\W]+/", "%", $_POST['terms']);
                $terms = "%" . $terms . "%";

                $users = Ode_DBO::getInstance()->query("
					SELECT " . DBO_User::COLUMNS . "
					FROM " . DBO_User::TABLE_NAME . " AS a
					WHERE (a.firstname LIKE " . Ode_DBO::getInstance()->quote($terms, PDO::PARAM_STR) . "
					OR a.lastname LIKE " . Ode_DBO::getInstance()->quote($terms, PDO::PARAM_STR) . "
					OR CONCAT(a.firstname, ' ', a.lastname) LIKE " . Ode_DBO::getInstance()->quote($terms, PDO::PARAM_STR) . "
					OR CONCAT(a.lastname, ' ', a.firstname) LIKE " . Ode_DBO::getInstance()->quote($terms, PDO::PARAM_STR) . ")
					AND a.id NOT IN (
						SELECT user_id
						FROM " . DBO_User_Organization_Cnx::TABLE_NAME . "
						WHERE org_id = " . Ode_DBO::getInstance()->quote($org_id, PDO::PARAM_INT) . "
					)
					LIMIT 0,10
				")->fetchAll(PDO::FETCH_CLASS, DBO_User::MODEL_NAME);

                Ode_View::getInstance()->assign("users", $users);
                Ode_View::getInstance()->assign("org_id", $org_id);

                echo Ode_View::getInstance()->fetch("ajax/orgs/user_assign_list.tpl.php");
                exit();
                break;
            case 'add':
                $org_id = $_POST['org_id'];
                $user_id = $_POST['user_id'];

                DBO_User_Organization_Cnx::addUserToOrg($user_id, $org_id);

                Util::json($_POST);
                exit();
                break;
        }
        break;
    case 'title':
        switch(Ode_Manager::getInstance()->getTask()) {
            case 'save':
                $org_id = $_POST['org_id'];
                $title = trim($_POST['title']);
                
                DBO_Organization::updateTitle($org_id, $title);
                
                Util::json($_POST);
                exit();
                break;
        }
        break;
}
?>