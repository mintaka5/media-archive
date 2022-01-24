<?php
require_once './init.php';

switch(Ode_Manager::getInstance()->getMode()) {
	default: break;
	case 'assets':
		switch(Ode_Manager::getInstance()->getTask()) {
			default: break;
			case 'get_all':
				$assets = Ode_DBO::getInstance()->query("
					SELECT " . DBO_Asset::COLUMNS . "
					FROM " . DBO_Asset::TABLE_NAME . " AS a
					WHERE a.is_deleted = 0
					ORDER BY a.modified
					DESC		
				")->fetchAll(PDO::FETCH_CLASS, DBO_Asset::MODEL_NAME);
				
				Ode_View::getInstance()->assign("assets", $assets);
				
				echo Ode_View::getInstance()->fetch("ajax/organize/asset-list.tpl.php");
				exit();
				break;
		}
		break;
	case 'groups':
		switch (Ode_Manager::getInstance()->getTask()) {
			default: break;
			case 'get_all':
				$groups = Ode_DBO::getInstance()->query("
					SELECT " . DBO_Group::COLUMNS . "
					FROM " . DBO_Group::TABLE_NAME . " AS a
					WHERE a.is_deleted = 0
					ORDER BY a.created
					DESC
				")->fetchAll(PDO::FETCH_CLASS, DBO_Group::MODEL_NAME);
				
				Ode_View::getInstance()->assign("groups", $groups);
				
				echo Ode_View::getInstance()->fetch("ajax/organize/group-list.tpl.php");
				exit();
				break;
			case 'get_one':
				$group = DBO_Group::getOneById($_POST['id']);
				
				Ode_View::getInstance()->assign("group", $group);
				
				echo Ode_View::getInstance()->fetch("ajax/organize/group-layout.tpl.php");
				exit();
				break;
		}
		break;
}
?>