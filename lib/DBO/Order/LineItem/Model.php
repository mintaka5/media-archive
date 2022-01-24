<?php
class DBO_Order_LineItem_Model {
	public $id;
	public $order_id;
	public $asset_id;
	public $is_approved;
	public $created;
	
	public function asset() {
		return DBO_Asset::getOneById($this->asset_id);
	}
}