<?php
class DBO_Asset_Outtake_Model {
	public $id;
	public $asset_id;
	
	public function asset() {
		return DBO_Asset::getOneById($this->asset_id);
	}
}