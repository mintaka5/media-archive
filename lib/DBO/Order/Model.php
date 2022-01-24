<?php
class DBO_Order_Model {
	public $id;
	public $order_id;
	public $user_id;
        public $is_active;
	public $is_deleted;
	public $created;
	
	public function lineitems() {
		return DBO_Order_LineItem::getAllByOrder($this->id);
	}
	
	public function user() {
		return DBO_User::getOneById($this->user_id);
	}
        
        public function hasDownloads() {
                $items = $this->lineitems();
                foreach($items as $item) {
                    if($item->is_approved == 1) {
                        return true;
                    }
                }
                
                return false;
        }
}