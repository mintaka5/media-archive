<?php
class Archive_Asset {
	private $_filename;
	
	public function __construct() {
		
	}
	
	public function setFilename($filename) {
		$this->_filename = $filename;
	}
	
	public function getFilename() {
		return $this->_filename;
	}
}
?>