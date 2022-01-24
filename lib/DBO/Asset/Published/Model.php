<?php
class DBO_Asset_Published_Model {
	public $id;
	public $asset_id;
	public $pub_id;
	public $user_id;
	public $created;
	
	public function asset() {
		return DBO_Asset::getOneById($this->asset_id);
	}
	
	public function publication() {
		return DBO_Publication::getOneById($this->pub_id);
	}
}