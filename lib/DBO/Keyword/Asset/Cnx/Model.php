<?php
class DBO_Keyword_Asset_Cnx_Model {
	public $id;
	public $keyword_id;
	public $asset_id;
	
	public function asset() {
		return DBO_Asset::getOneById($this->asset_id);
	}
	
	public function keyword() {
		return DBO_Keyword::getOneById($this->keyword_id);
	}
}