<?php
class DBO_Asset_Restriction_Embargo_Model {
	public $id;
	public $asset_id;
	public $start_date;
	public $created_by;
	public $created;
	
	public function user() {
		return DBO_User::getOneById($this->created_by);
	}
	
	public function asset() {
		return DBO_Asset::getOneById($this->asset_id);
	}
}
?>