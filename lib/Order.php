<?php
class Order {
	const SESSION_NAME = "orderID";
	private $user_id;
    private static $_instance;
	
	public function __construct($user_id) {
		$this->setUserId($user_id);
		
		if(!isset($_SESSION[self::SESSION_NAME])) {
			$order = DBO_Order::getOneActiveByUser($user_id);
			
			if($order != false) {
				$orderId = $order->id;
			} else {
				$orderId = $this->generate();
			}
			
			$_SESSION[self::SESSION_NAME] = $orderId;
		}
                
                self::$_instance = $this;
	}
	
        public static function getInstance() {
            return self::$_instance;
        }


        private function generate() {
		$order = DBO_Order::getOneActiveByUser($this->getUserId());
		
		if($order == false) {
			// create order and assign to return
			return DBO_Order::add($this->getUserId());
		} else {
			// assign to return
			return $order->id;
		}
	}
	
	private function setUserId($user_id) {
		$this->user_id = $user_id;
	}
	
	private function getUserId() {
		return $this->user_id;
	}
	
	public function getOrderId() {
		return $_SESSION[self::SESSION_NAME];
	}
	
	public function remove() {
		DBO_Order::delete(self::getOrderId());
		
		self::removeFromSession();
		
		return;
	}
	
	public function removeFromSession() {
		unset($_SESSION[self::SESSION_NAME]);
	}
}