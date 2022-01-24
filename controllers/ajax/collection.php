<?php
require_once './init.php';

switch(Ode_Manager::getInstance()->getMode()) {
	default: break;
	case 'approval':
		switch(Ode_Manager::getInstance()->getTask()) {
			default:
				$container_id = $_POST['container_id'];
				$appr = $_POST['appr'];
				
				$update = DBO_Container::setApproval($container_id, $appr);
				
				Util::json(array("update" => $update, "approval" => $appr, "status" => ($appr==1) ? "Public" : "Private"));
				exit();
				break;
		}
		break;
	case 'list':
		switch(Ode_Manager::getInstance()->getTask()) {
			default: break;
			case 'groups':
				$container_id = $_GET['container_id'];
				
				$groups = DBO_Container::getGroups($container_id);
				
				Ode_View::getInstance()->assign("container_id", $container_id);
				Ode_View::getInstance()->assign("groups", $groups);
				
				echo Ode_View::getInstance()->fetch("ajax/collection/list/groups.tpl.php");
				exit();
				break;
		}
		break;
	case 'title':
		switch (Ode_Manager::getInstance()->getTask()) {
			default: break;
			case 'update':
				$container_id = $_POST['container_id'];
				$title = trim($_POST['title']);
				
				$sth = Ode_DBO::getInstance()->prepare("UPDATE " . DBO_Container::TABLE_NAME . " SET title = :title WHERE id = :id");
				$sth->bindParam(":title", $title, PDO::PARAM_STR, 45);
				$sth->bindParam(":id", $container_id, PDO::PARAM_STR, 50);
				
				try {
					$sth->execute();
				} catch(Exception $e) {
					error_log($e->getTraceAsString(), 0);
				} catch(PDOException $e) {
					error_log($e->getTraceAsString(), 0);
				}
				
				Util::json($_POST);
				exit();
				break;
		}
		break;
	case 'desc':
		switch(Ode_Manager::getInstance()->getTask()) {
			default: break;
			case 'update':
				$container_id = $_POST['container_id'];
				$desc = $_POST['desc'];
				
				$sth = Ode_DBO::getInstance()->prepare("UPDATE " . DBO_Container::TABLE_NAME . " SET description = :desc WHERE id = :id");
				$sth->bindParam(":desc", $desc, PDO::PARAM_STR, 255);
				$sth->bindParam(":id", $container_id, PDO::PARAM_STR, 50);
				
				try {
					$sth->execute();
				} catch(Exception $e) {
					error_log($e->getTraceAsString(), 0);
				} catch(PDOException $e) {
					error_log($e->getTraceAsString(), 0);
				}
				
				Util::json($_POST);
				exit();
				break;
		}
		break;
	case 'sets':
		switch(Ode_Manager::getInstance()->getTask()) {
			default:
				
				break;
			case 'remove':
				$container_id = $_POST['container_id'];
				$group_id = $_POST['group_id'];
				
				$rem = DBO_Container_Metadata::delete($container_id, DBO_Container_Metadata::META_GROUP_ID_NAME, $group_id);
				
				Util::json($rem);
				exit();
				break;
			case 'add':
				$container_id = $_POST['container_id'];
				$group_id = $_POST['group_id'];
				
				$add = DBO_Container_Metadata::add(DBO_Container_Metadata::META_GROUP_ID_NAME, $group_id, $container_id);
				
				Util::json($_POST);
				exit();
				break;
			case 'all':		
				$container_id = $_GET['container_id'];
				
				/**
				 * only admins have access to all collections globally
				 */
				if(Ode_Auth::getInstance()->isAdmin()) {
					$sql = "SELECT " . DBO_Group::COLUMNS . " 
							FROM " . DBO_Group::TABLE_NAME . " AS a
							WHERE a.is_deleted = 0
							AND a.id NOT IN (
								SELECT meta_value
								FROM " . DBO_Container_Metadata::TABLE_NAME . "
								WHERE container_id = " . Ode_DBO::getInstance()->quote($container_id, PDO::PARAM_STR) . "
								AND meta_key = " . Ode_DBO::getInstance()->quote(DBO_Container_Metadata::META_GROUP_ID_NAME, PDO::PARAM_STR) . " 
							)";
				} else {
					$org_ids = Util::dbQuoteListItems(DBO_User_Organization_Cnx::getUserOrgIDs(Ode_Auth::getInstance()->getSession()->id));
					$org_ids = (!empty($org_ids)) ? implode(',', $org_ids) : "''";
					
					$sql = "SELECT " . DBO_Group::COLUMNS . "
							FROM " . DBO_Group::TABLE_NAME . " AS a
							LEFT JOIN " . DBO_Group_Metadata::TABLE_NAME . " AS b ON (b.group_id = a.id) 
							WHERE a.is_deleted = 0
							AND b.id IS NOT NULL
							AND b.meta_name = " . Ode_DBO::getInstance()->quote(DBO_Group_Metadata::META_ORG_ID_NAME, PDO::PARAM_STR) . "
							AND b.meta_value IN (" . $org_ids . ")
							AND a.id NOT IN (
								SELECT meta_value
								FROM " . DBO_Container_Metadata::TABLE_NAME . "
								WHERE container_id = " . Ode_DBO::getInstance()->quote($container_id, PDO::PARAM_STR) . "
								AND meta_key = " . Ode_DBO::getInstance()->quote(DBO_Container_Metadata::META_GROUP_ID_NAME, PDO::PARAM_STR) . " 
							)";
				}
				
				//Util::debug($sql);
				
				$groups = Ode_DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_CLASS, DBO_Group::MODEL_NAME);
				
				Ode_View::getInstance()->assign("groups", $groups);
				Ode_View::getInstance()->assign("container_id", $container_id);
				
				echo Ode_View::getInstance()->fetch("ajax/collection/list/addgroups.tpl.php");
				break;
			case 'search':
				$container_id = $_POST['container_id'];
				
				$terms = trim($_POST['terms']);
				$terms = preg_replace("/[\s\t\r\n\W]+/", "%", $terms);
				//Util::debug($terms);
				
				/**
				 * only admins have access to collections globally
				 */
				if(Ode_Auth::getInstance()->isAdmin()) {
					$sql = "SELECT " . DBO_Group::COLUMNS . " 
							FROM " . DBO_Group::TABLE_NAME . " AS a 
							WHERE a.is_deleted = 0
							AND a.title LIKE '%" . $terms . "%'
							AND a.id NOT IN (
								SELECT meta_value
								FROM " . DBO_Container_Metadata::TABLE_NAME . "
								WHERE container_id = " . Ode_DBO::getInstance()->quote($container_id, PDO::PARAM_STR) . "
								AND meta_key = " . Ode_DBO::getInstance()->quote(DBO_Container_Metadata::META_GROUP_ID_NAME, PDO::PARAM_STR) . " 
							)";
				} else {
					$org_ids = Util::dbQuoteListItems(DBO_User_Organization_Cnx::getUserOrgIDs(Ode_Auth::getInstance()->getSession()->id));
					$org_ids = (!empty($org_ids)) ? implode(',', $org_ids) : "''";
						
					$sql = "SELECT " . DBO_Group::COLUMNS . "
							FROM " . DBO_Group::TABLE_NAME . " AS a
							LEFT JOIN " . DBO_Group_Metadata::TABLE_NAME . " AS b ON (b.group_id = a.id)
							WHERE a.is_deleted = 0
							AND b.id IS NOT NULL
							AND b.meta_name = " . Ode_DBO::getInstance()->quote(DBO_Group_Metadata::META_ORG_ID_NAME, PDO::PARAM_STR) . "
							AND b.meta_value IN (" . $org_ids . ")
							AND a.title LIKE '%" . $terms . "%'
							AND a.id NOT IN (
								SELECT meta_value
								FROM " . DBO_Container_Metadata::TABLE_NAME . "
								WHERE container_id = " . Ode_DBO::getInstance()->quote($container_id, PDO::PARAM_STR) . "
								AND meta_key = " . Ode_DBO::getInstance()->quote(DBO_Container_Metadata::META_GROUP_ID_NAME, PDO::PARAM_STR) . " 
							)";
				}
				//echo $sql;
				
				$groups = Ode_DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_CLASS, DBO_Group::MODEL_NAME);
					
				Ode_View::getInstance()->assign("container_id", $container_id);
				Ode_View::getInstance()->assign("groups", $groups);
					
				echo Ode_View::getInstance()->fetch("ajax/collection/list/addgroups.tpl.php");
				break;
		}
		break;
}
?>