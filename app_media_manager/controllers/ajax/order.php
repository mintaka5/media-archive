<?php
require_once './init.php';

switch(Ode_Manager::getInstance()->getMode()) {
    default:
        
        break;
    case 'del':
        DBO_Order::delete($_POST['order']);
        
        Util::json($_POST);
        exit();
        break;
    case 'notify':
        $order = DBO_Order::getOneById($_POST['order']);
        
        $mailer = new UCI_Mailer();
        $mailer->addTo($order->user()->email, $order->user()->fullname());
        $mailer->setFrom(Ode_Auth::getInstance()->getSession()->email);
        $mailer->setSubject("Image Archive Request ".$order->order_id." is ready for review.");
        
        Ode_View::getInstance()->assign("message", trim($_POST['msg']));
        Ode_View::getInstance()->assign("order", $order);
        Ode_View::getInstance()->assign("linkback", "http://".APP_DOMAIN.Ode_Manager::getInstance()->action("account", "orders", "view", array("id", $order->id)));
        $mailer->setHTMLBody(Ode_View::getInstance()->fetch("mail/requestNotify.tpl.php"));
        
        try {
            $mailer->send();
        } catch(Exception $e) {
            //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
            error_log($e->getMessage(), 0);

            exit($e->getMessage());
        }
        
        Util::json($_POST);
        exit();
        break;
}
?>
