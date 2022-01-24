<?php
if(!Ode_Auth::getInstance()->isPrivate()) {
	exit("You do not have sufficient privileges to enter this page.");
}

switch(Ode_Manager::getInstance()->getMode()) {
	default:
		switch(Ode_Manager::getInstance()->getTask()) {
			default:
				$orders = Ode_DBO::getInstance()->query("
                	SELECT " . DBO_Order::COLUMNS . "
                	FROM " . DBO_Order::TABLE_NAME . " AS a
                    WHERE a.is_deleted = 0
                    AND a.is_active = 0
                    ORDER BY a.created
                    DESC
                ")->fetchAll(PDO::FETCH_CLASS, DBO_Order::MODEL_NAME);
                            
                $pager = Pager::factory(array(
					'perPage' => 20,
					'urlVar' => "page",
					'mode' => "Sliding",
					'append' => true,
					'path' => Ode_Manager::getInstance()->action("orders"),
					'delta' => 5,
					'itemData' => $orders
				));
                                
                Ode_View::getInstance()->assign("orders", $pager->getPageData());
                Ode_View::getInstance()->assign("pagelinks", $pager->getLinks());
				break;
		}
		break;
	case 'view':
		switch(Ode_Manager::getInstance()->getTask()) {
			default:
				$order = DBO_Order::getOneById($_GET['id']);
			
				
				Ode_View::getInstance()->assign("order", $order);
				break;
		}
		break;
	case 'search':
		$rawQry = trim($_GET['q']);
		$qry = "%" . preg_replace("/[\s\t\n\r\W]+/", "%", $rawQry) . "%";
		
		$orders = Ode_DBO::getInstance()->query("
			SELECT " . DBO_Order::COLUMNS . "
			FROM " . DBO_Order::TABLE_NAME . " AS a
			LEFT JOIN " . DBO_User::TABLE_NAME . " AS b ON (b.id = a.user_id)
			WHERE a.order_id = " . Ode_DBO::getInstance()->quote($rawQry, PDO::PARAM_STR) . "
			OR b.username LIKE " . Ode_DBO::getInstance()->quote($qry, PDO::PARAM_STR) . "
			OR b.firstname LIKE " . Ode_DBO::getInstance()->quote($qry, PDO::PARAM_STR) . "
			OR b.lastname LIKE " . Ode_DBO::getInstance()->quote($qry, PDO::PARAM_STR) . "
		")->fetchAll(PDO::FETCH_CLASS, DBO_Order::MODEL_NAME);
		
		$pager = Pager::factory(array(
			'perPage' => 20,
			'urlVar' => "page",
			'mode' => "Sliding",
			'append' => true,
			'path' => Ode_Manager::getInstance()->action("orders"),
			'delta' => 5,
			'itemData' => $orders
		));
		
		Ode_View::getInstance()->assign("orders", $pager->getPageData());
		Ode_View::getInstance()->assign("pagelinks", $pager->getLinks());
		break;
}
?>