<?php
if(!Ode_Auth::getInstance()->isPrivate()) {
	exit("You do not have sufficient privileges to enter this page.");
}

$pageId = (isset($_GET['pageID'])) ? $_GET['pageID'] : 1;

switch(Ode_Manager::getInstance()->getMode()) {
	default: 
		$user_orgs = Ode_Auth::getInstance()->getSession()->organizations();
		Ode_View::getInstance()->assign("user_orgs", $user_orgs);
		
		//$containers = DBO_Container::getAll("created", "DESC");
		$containers = DBO_Container::getAllByUserOrgs(Ode_Auth::getInstance()->getSession()->id);
		
		Ode_View::getInstance()->assign("containers", $containers);
		break;
	case 'add':
		$uuid = Ode_DBO::getInstance()->query("SELECT UUID()")->fetchColumn();
		
		$sth = Ode_DBO::getInstance()->prepare("
			INSERT INTO " . DBO_Container::TABLE_NAME . " (id, title, is_approved, is_deleted, created, modified, modified_by)
			VALUES (:id, :title, 0, 0, NOW(), NOW(), :user)
		");
		$sth->bindParam(":id", $uuid, PDO::PARAM_STR, 50);
		$sth->bindParam(":title", trim($_POST['collTitle']), PDO::PARAM_STR, 45);
		$sth->bindParam(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR, 50);
		
		try {
			$sth->execute();
			
			if(!isset($_POST['userOrg'])) {
				DBO_Container::assignOrganizationsByUser($uuid, Ode_Auth::getInstance()->getSession()->id);
			} else {
				foreach($_POST['userOrg'] as $org_id) {
					DBO_Container_Metadata::add(DBO_Container_Metadata::META_ORG_ID_NAME, $org_id, $uuid);
				}
			}
			
			header("Location: " . Ode_Manager::getInstance()->action("collection", "view", null, array("id", $uuid)));
			exit();
		} catch(PDOException $e) {
			error_log($e->getTraceAsString(), 0);
		} catch(Exception $e) {
			error_log($e->getTraceAsString(), 0);
		}
		break;
}
?>