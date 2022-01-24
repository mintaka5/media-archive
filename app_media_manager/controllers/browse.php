<?php
switch(Ode_Manager::getInstance()->getMode()) {
	default:
		
		break;
	case 'groups':
		switch(Ode_Manager::getInstance()->getTask()) {
			default:
				$groups = Ode_DBO::getInstance()->query("
					SELECT " . DBO_Group::COLUMNS . "
					FROM " . DBO_Group::TABLE_NAME . " AS a
					WHERE a.is_approved = 1
					AND a.is_deleted = 0
					AND (
						SELECT COUNT(*) 
						FROM " . DBO_Asset_Group_Cnx::TABLE_NAME . " 
						LEFT JOIN " . DBO_Asset::TABLE_NAME . " ON (" . DBO_Asset::TABLE_NAME . ".id = " . DBO_Asset_Group_Cnx::TABLE_NAME . ".asset_id)
						WHERE group_id = a.id
						AND " . DBO_Asset::TABLE_NAME . ".is_active = 1
						AND " . DBO_Asset::TABLE_NAME . ".is_deleted = 0
					) > 0
					ORDER BY a.created
					DESC
				")->fetchAll(PDO::FETCH_CLASS, DBO_Group::MODEL_NAME);
			
				$pager = Pager::factory(array(
					'perPage' => 12,
					'urlVar' => "page",
					'mode' => "Sliding",
					'append' => true,
					'path' => Ode_Manager::getInstance()->action("browse", "groups"),
					'delta' => 5,
					'itemData' => $groups
				));
				
				Ode_View::getInstance()->assign("groups", $pager->getPageData());
				Ode_View::getInstance()->assign("pagelinks", $pager->getLinks());
				break;
		}
		break;
}
?>