<?php
class Metadata {
	private $_filename;
	private $_exiftool;
	
	public function __construct($filename) {
		$this->setFilename($filename);
		
		$this->setExifTool(EXIFTOOL_PATH);
	}
	
	public function setFilename($filename) {
		$this->_filename = escapeshellarg($filename);
	}
	
	public function getFilename() {
		return $this->_filename;
	}
	
	private function setExifTool($path) {
		$this->_exiftool = $path;
	}
	
	public function getExifTool() {
		return $this->_exiftool;
	}
}