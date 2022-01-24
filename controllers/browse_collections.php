<?php
switch(Ode_Manager::getInstance()->getMode()) {
	default:
		switch (Ode_Manager::getInstance()->getTask()) {
			default:
				$collections = DBO_Container::getAllPublic();
				
				$pager = Pager::factory(array(
					'perPage' => 10,
					'urlVar' => 'page',
					'mode' => 'Sliding',
					'append' => true,
					'path' => Ode_Manager::getInstance()->action("browse_collections"),
					'delta' => 5,
					'itemData' => $collections
				));

				Ode_View::getInstance()->assign("listdata", $pager->getPageData());
				Ode_View::getInstance()->assign("pagelinks", $pager->getLinks());
				break;
		}
		break;
	case 'view':
		$collection = DBO_Container::getOneById(trim($_GET['id']));
	
		Ode_View::getInstance()->assign("collection", $collection);
		Ode_View::getInstance()->assign("groups", $collection->publicGroups());
		break;
	case 'group':
		switch(Ode_Manager::getInstance()->getTask()) {
			default:
				
				break;
			case 'view':
				$collection = DBO_Container::getOneById($_GET['cid']);
				$group = DBO_Group::getOneById($_GET['id']);
				
				$sql = "SELECT " . DBO_Asset::COLUMNS . " FROM view_publicGroupImages AS a WHERE a.group_id = " . Ode_DBO::getInstance()->quote($group->id). "";
				
				$assets = Ode_DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_CLASS, DBO_Asset::MODEL_NAME);
				
				$pager = Pager::factory(array(
						'perPage' => 9,
						'urlVar' => "page",
						'mode' => "Sliding",
						'append' => true,
						'path' => Ode_Manager::getInstance()->action("browse_collections", "group", "view", array("id", $group->id), array("cid", $collection->id)),
						'delta' => 5,
						'itemData' => $assets
				));
				
				Ode_View::getInstance()->assign("group", $group);
				Ode_View::getInstance()->assign("collection", $collection);
				Ode_View::getInstance()->assign("assets", $pager->getPageData());
				Ode_View::getInstance()->assign("pagelinks", $pager->getLinks());
				break;
		}
		break;
}
?>