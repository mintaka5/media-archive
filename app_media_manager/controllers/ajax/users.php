<?php
require_once './init.php';

switch(Ode_Manager::getInstance()->getMode()) {
	case 'type':
		switch(Ode_Manager::getInstance()->getTask()) {
			case 'edit':
				DBO_User_Type_Cnx::update($_POST['user'], $_POST['type']);
				
				Util::json($_POST);
				exit();
				break;
		}
		break;
	case 'org':
		switch(Ode_Manager::getInstance()->getTask()) {
			default:
				break;
			case 'rmv':
				DBO_User_Organization_Cnx::removeUserFromOrg($_POST['user_id'], $_POST['org_id']);
				
				Util::json($_POST);
				exit();
				break;
			case 'add':
				$cnx = DBO_User_Organization_Cnx::addUserToOrg($_POST['user_id'], $_POST['org_id']);
				$user = DBO_User::getOneById($_POST['user_id']);
				
				Ode_View::getInstance()->assign("user", $user);
				
				echo Ode_View::getInstance()->fetch("ajax/users/org_list.tpl.php");
				exit();
				break;
		}
		break;
	case 'del':
		switch (Ode_Manager::getInstance()->getTask()) {
			default:
				DBO_User::delete($_POST['user']);
			
				Util::json($_POST);
				exit();
				break;
		}
		break;
}
?>