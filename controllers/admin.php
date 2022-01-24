<?php
switch(Ode_Manager::getInstance()->getMode()) {
	case false:
	default:
		switch (Ode_Manager::getInstance()->getTask()) {
			case false:
			default:
				$recents = Ode_DBO::getInstance()->query("
					SELECT " . DBO_Asset::COLUMNS . "
					FROM " . DBO_Asset::TABLE_NAME . " AS a
					WHERE a.is_deleted = 0
					ORDER BY a.created
					DESC
					LIMIT 0,5
				")->fetchAll(PDO::FETCH_CLASS, DBO_Asset::MODEL_NAME);
				
				Ode_View::getInstance()->assign("recents", $recents);
				
				$groups = Ode_DBO::getInstance()->query("
					SELECT " . DBO_Group::COLUMNS . "
					FROM " . DBO_Group::TABLE_NAME . " AS a
					WHERE is_deleted = 0
					ORDER BY a.created
					DESC
					LIMIT 0,5
				")->fetchAll(PDO::FETCH_CLASS, DBO_Group::MODEL_NAME);
				
				Ode_View::getInstance()->assign("groups", $groups);
				
				break;
		}
		break;
}
?>