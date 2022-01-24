<?php
class DBO_Asset_Group_Cnx_Model {
	public $id;
	public $asset_id;
	public $group_id;
	
	public function group() {
		return DBO_Group::getOneById($this->group_id);
	}
	
	public function asset() {
		return DBO_Asset::getOneById($this->asset_id);
	}
	
	public function definition() {
		return DBO_Asset_Group_Def::getOneByCnxId($this->id);
	}
}