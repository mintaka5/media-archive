<?php
require_once './init.php';

switch(Ode_Manager::getInstance()->getMode()) {
	default:
		switch(Ode_Manager::getInstance()->getTask()) {
			case 'add':
				$orderId = Order::getInstance()->getOrderId();
				DBO_Order_LineItem::add($orderId, $_POST['asset']);
				
				Util::json(array("formdata" => $_POST, "order" => $orderId));
				exit();
				break;
                        case 'del':
                            $lineitem = DBO_Order_LineItem::getOneByOrderAndAsset(Order::getInstance()->getOrderId(), $_POST['asset']);
                            
                            if($lineitem != false) {
                                DBO_Order_LineItem::deleteById($lineitem->id);
                            }
                            
                            Util::json(array("formdata" => $_POST, "lineitem_id" => $lineitem->id));
                            exit ();
                            break;
		}
		break;
		case 'approve':
			switch (Ode_Manager::getInstance()->getTask()) {
				default:
				case 'yes':
					DBO_Order_LineItem::approve($_POST['item']);
					
				Util::json(array("formdata" => $_POST, "approved" => 1, "text" => "Approved", "className" => "approved"));
				exit();
				break;
				case 'no':
					DBO_Order_LineItem::approve($_POST['item'], 0);
		
					Util::json(array("formdata" => $_POST, "approved" => 0, "text" => "Rejected", "className" => "rejected"));
					exit();
					break;
			}
			break;
}
?>