<?php
class DBO_Asset_Group_Def_Model {
	public $id;
	public $cnx_id;
	public $width;
	public $height;
	public $is_default;
	
	public function connection() {
		return DBO_Asset_Group_Cnx::getOneById($this->cnx_id);
	}
}