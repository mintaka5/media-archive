<?php
/**
 * Allow only administrators and managers
 */
if(!Ode_Auth::getInstance()->isAdmin() && !Ode_Auth::getInstance()->isManager()) {
	header("Location: " . Ode_Manager::getInstance()->action("index"));
	exit();
}

/*$types = Ode_DBO::getInstance()->query("
	SELECT " . DBO_User_Type::COLUMNS . "
	FROM " . DBO_User_Type::TABLE_NAME . " AS a
	WHERE a.is_deleted = 0
	ORDER BY a.title
	ASC
")->fetchAll(PDO::FETCH_CLASS, DBO_User_Type::MODEL_NAME);*/
$types = DBO_User_Type::getAllByUserType(Ode_Auth::getInstance()->getSession()->id);

switch (Ode_Manager::getInstance()->getMode()) {
	default:
		switch(Ode_Manager::getInstance()->getTask()) {
			default:
				$users = DBO_User::getAllByUserOrgs(Ode_Auth::getInstance()->getSession()->id);
				
				/*$users = Ode_DBO::getInstance()->query("
					SELECT " . DBO_User::COLUMNS . "
					FROM " . DBO_User::TABLE_NAME . " AS a
					WHERE a.is_deleted = 0
					ORDER BY a.lastname
					ASC
				")->fetchAll(PDO::FETCH_CLASS, DBO_User::MODEL_NAME);*/
				
				$pager = Pager::factory(array(
					'perPage' => 20,
					'urlVar' => "pageNum",
					'mode' => "Sliding",
					'append' => true,
					'delta' => 4,
					'itemData' => $users
				));
				
				Ode_View::getInstance()->assign("usertypes", $types);
				Ode_View::getInstance()->assign("users", $pager->getPageData());
				Ode_View::getInstance()->assign("pagelinks", $pager->getLinks());
				break;
		}
		break;
	case 'search':
		switch(Ode_Manager::getInstance()->getTask()) {
			default:
				$qry = trim($_GET['txtSearchUsers']);
				$qry = "%" . preg_replace("/[\s\t\n\r\W]+/", "%", $qry) . "%";	
				
				$users = Ode_DBO::getInstance()->query("
					SELECT " . DBO_User::COLUMNS . "
					FROM " . DBO_User::TABLE_NAME . " AS a
					WHERE a.is_active = 1
					AND a.is_deleted = 0
					AND (a.firstname LIKE " . Ode_DBO::getInstance()->quote($qry, PDO::PARAM_STR) . "
					OR a.lastname LIKE " . Ode_DBO::getInstance()->quote($qry, PDO::PARAM_STR) . "
					OR a.email LIKE " . Ode_DBO::getInstance()->quote($qry, PDO::PARAM_STR) . "
					OR a.username LIKE " . Ode_DBO::getInstance()->quote($qry, PDO::PARAM_STR) . ")
					ORDER BY a.lastname
					ASC
				")->fetchAll(PDO::FETCH_CLASS, DBO_User::MODEL_NAME);
				
				$pager = Pager::factory(array(
					'perPage' => 20,
					'urlVar' => "pageNum",
					'mode' => "Sliding",
					'append' => true,
					'path' => Ode_Manager::getInstance()->action("users", "search", null, array("txtSearchUsers", trim($_GET['txtSearchUsers']))),
					'delta' => 4,
					'itemData' => $users
				));
				
				Ode_View::getInstance()->assign("usertypes", $types);
				Ode_View::getInstance()->assign("users", $pager->getPageData());
				Ode_View::getInstance()->assign("pagelinks", $pager->getLinks());
				break;
		}
		break;
	case 'edit':
		switch(Ode_Manager::getInstance()->getTask()) {
			default:
				$user = DBO_User::getOneById($_GET['id']);
				$usertypes = DBO_User_Type::getAllActive();
				$allorgs = DBO_Organization::getAllActive();
				
				Ode_View::getInstance()->assign("user", $user);
				Ode_View::getInstance()->assign("usertypes", $usertypes);
				Ode_View::getInstance()->assign("allorgs", $allorgs);
				break;
		}
		break;
}
?>