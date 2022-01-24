<?php
class Metadata_PEL {
	private $_ifd = null;
	
	public function __construct($data = false) {
		$memLimit = ini_get("memory_limit");
		
		/**
		 * increase memory handling for large TIFF files
		 */
		ini_set("memory_limit", "128M");
		
		/**
		 * Because we're handling TIFF and JPEG
		 * and each type stores data at different points
		 * in the binary stream, we need to handle with two different
		 * objects
		 */
		try {
			$pel = new PelTiff($data);
		} catch(Exception $e) {
			//echo $e->getMessage();
            error_log($e->getMessage(), 0);
		}
		
		try {
			$pel = new PelJpeg($data);
		} catch(Exception $e) {
			//echo $e->getMessage();
            error_log($e->getMessage(), 0);
		}
		
		if($pel instanceof PelJpeg) {
			//Util::debug($pel->getExif());
			$this->setIfd($pel->getExif()->getTiff()->getIfd());
		}
		
		if($pel instanceof PelTiff) {
			$this->setIfd($pel->getIfd());
		}
		
		/**
		 * reset memory limit
		 */
		ini_set("memory_limit", $memLimit);
	}
	
	public function getMake() {
		if($this->hasIfd()) {
			$make = $this->getIfd()->getEntry(PelTag::MAKE);
			
			if(!is_null($make)) {
				return $make->getValue();
			}
		}
		
		return "";
	}
	
	public function getModel() {
		if($this->hasIfd()) {
			$model = $this->getIfd()->getEntry(PelTag::MODEL);
			
			if(!is_null($model)) {
				return $model->getValue();
			}
		}
		
		return "";
	}
	
	private function setIfd(PelIfd $ifd) {
		/**
		 * have to ensure that the TIFF information exists
		 * within the image
		 */
		$this->_ifd = $ifd;
	}
	
	public function getShutterSpeed() {
		if($this->hasIfd()) {
			$shutterSpd = $this->getIfd()->getEntry(PelTag::SHUTTER_SPEED_VALUE);
			
			if(!is_null($shutterSpd)) {
				return $shutterSpd->getValue();
			}
		}
		
		return "";
	}
	
	public function getFStop() {
		if($this->hasIfd()) {
			return $this->getIfd()->getEntry(PelTag::FNUMBER)->getValue();
		}
		
		return "";
	}
	
	private function getIfd() {
		return $this->_ifd;
	}
	
	private function hasIfd() {
		if($this->getIfd() != null) {
			return true;
		}
		
		return false;
	}
}