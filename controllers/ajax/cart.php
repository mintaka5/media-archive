<?php
require_once './init.php';

switch(Ode_Manager::getInstance()->getMode()) {
	default:
		switch(Ode_Manager::getInstance()->getTask()) {
			default:
				
				break;
		}
		break;
	case 'preview':
		$items = Ode_DBO::getInstance()->query("
			SELECT " . DBO_Order_LineItem::COLUMNS . "
			FROM " . DBO_Order_LineItem::TABLE_NAME . " AS a
			LEFT JOIN " . DBO_Asset::TABLE_NAME . " AS b ON (b.id = a.asset_id)
			WHERE a.order_id = " . Ode_DBO::getInstance()->quote(Order::getInstance()->getOrderId(), PDO::PARAM_STR) . "
			ORDER BY a.created
			DESC
			LIMIT 0,5
		")->fetchAll(PDO::FETCH_CLASS, DBO_Order_LineItem::MODEL_NAME);
		
		Ode_View::getInstance()->assign("items", $items);
		
		echo Ode_View::getInstance()->fetch("ajax/cartPreview.tpl.php");
		exit();
		break;
	case 'item':
		switch(Ode_Manager::getInstance()->getTask()) {
			default: break;
			case 'del':
				$result = DBO_Order_LineItem::deleteById($_POST['item']);
				
				$items = DBO_Order_LineItem::getAllByOrder(Order::getInstance()->getOrderId());
				$item_count = count($items);
				
				Util::json(array("formdata" => $_POST, "num_items" => $item_count, "success" => $result));
				exit();
				break;
		}
		break;
}
?>