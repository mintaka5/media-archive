<?php
class Metadata_Exif extends Metadata {
	const APERTURE_VALUE = "ApertureValue";
	const ARTIST = "Artist";
	const BRIGHTNESS_VALUE = "BrightnessValue";
	const COPYRIGHT = "Copyright";
	const DATE_TIME_ORIGINAL = "DateTimeOriginal";
	const EXPOSURE_PROGRAM = "ExposureProgram";
	const EXPOSURE_TIME = "ExposureTime";
	const F_NUMBER = "FNumber";
	const FLASH = "Flash";
	const FOCAL_LENGTH = "FocalLength";
	const IMAGE_DESCRIPTION = "ImageDescription";
	const IMAGE_HEIGHT = "ImageHeight";
	const IMAGE_WIDTH = "ImageWidth";
	const ISO_SPEED = "ISOSpeed";
	const MAKE = "Make";
	const MODEL = "Model";
	const MODIFY_DATE = "ModifyDate";
	const ORIENTATION = "Orientation";
	const PROCESSING_SOFTWARE = "ProcessingSoftware";
	const SHUTTER_SPEED_VALUE = "ShutterSpeedValue";
	const SOFTWARE = "Software";
	const X_RESOLUTION = "XResolution";
	const Y_RESOLUTION = "YResolution";
	
	private $_orientations;
	private $_exposures;
	
	public function __construct($filename) {
		parent::__construct($filename);
		
		$this->_orientations = new ArrayObject();
		$this->_orientations->offsetSet(1, "Horizontal");
		$this->_orientations->offsetSet(2, "Mirror horizontal");
		$this->_orientations->offsetSet(3, "Rotated 180");
		$this->_orientations->offsetSet(4, "Mirror vertical");
		$this->_orientations->offsetSet(5, "Mirror horizontal and rotated 270 clockwise");
		$this->_orientations->offsetSet(6, "Rotated 90 clockwise");
		$this->_orientations->offsetSet(7, "Mirror horizontal and rotated 90  clockwise");
		$this->_orientations->offsetSet(8, "Rotated 270 clockwise");
		
		$this->_exposures = new ArrayObject();
		$this->_exposures->offsetSet(0, "Not defined");
		$this->_exposures->offsetSet(1, "Manual");
		$this->_exposures->offsetSet(2, "Program AE");
		$this->_exposures->offsetSet(3, "Aperture-priority AE");
		$this->_exposures->offsetSet(4, "Shutter speed priority AE");
		$this->_exposures->offsetSet(5, "Creative (slow speed)");
		$this->_exposures->offsetSet(6, "Action (High speed)");
		$this->_exposures->offsetSet(7, "Portrait");
		$this->_exposures->offsetSet(8, "Landscape");
		$this->_exposures->offsetSet(9, "Bulb");
	}
	
	public function toHtml() {
		$output = shell_exec($this->getExifTool() . " -EXIF:all -h -filename " . $this->getFilename());
		
		return $output;
	}
	
	public function getProcessingSoftware() {
		return shell_exec($this->getExifTool() . " -EXIF:" . self::PROCESSING_SOFTWARE . " " . $this->getFilename());
	}
	
	public function getImageWidth() {
		return shell_exec($this->getExifTool() . " -EXIF:" . self::IMAGE_WIDTH . " " . $this->getFilename());
	}
	
	public function getImageHeight() {
		return shell_exec($this->getExifTool() . " -EXIF:" . self::IMAGE_HEIGHT . " " . $this->getFilename());
	}
	
	public function getImageDescription() {
		return shell_exec($this->getExifTool() . " -EXIF:" . self::IMAGE_DESCRIPTION . " " . $this->getFilename());
	}
	
	public function getMake() {
		return shell_exec($this->getExifTool() . " -EXIF:" . self::MAKE . " " . $this->getFilename());
	}
	
	public function getModel() {
		return shell_exec($this->getExifTool() . " -EXIF:" . self::MODEL . " " . $this->getFilename());
	}
	
	public function getOrientation() {
		$result = shell_exec($this->getExifTool() . " -EXIF:" . self::ORIENTATION . " " . $this->getFilename());
		
		return $this->_orientations->offsetGet($result);
	}
	
	public function getXResolution() {
		return shell_exec($this->getExifTool() . " -EXIF:" . self::X_RESOLUTION . " " . $this->getFilename());
	}
	
	public function getYResolution() {
		return shell_exec($this->getExifTool() . " -EXIF:" . self::Y_RESOLUTION . " " . $this->getFilename());
	}
	
	public function getSoftware() {
		return shell_exec($this->getExifTool() . " -EXIF:" . self::SOFTWARE . " " . $this->getFilename());
	}
	
	public function getModifyDate() {
		return shell_exec($this->getExifTool() . " -EXIF:" . self::MODIFY_DATE . " " . $this->getFilename());
	}
	
	public function getArtist() {
		return shell_exec($this->getExifTool() . " -EXIF:" . self::ARTIST . " " . $this->getFilename());
	}
	
	public function getCopyright() {
		return shell_exec($this->getExifTool() . " -EXIF:" . self::COPYRIGHT . " " . $this->getFilename());
	}
	
	public function getExposureTime() {
		return shell_exec($this->getExifTool() . " -EXIF:" . self::EXPOSURE_TIME . " " . $this->getFilename());
	}
	
	public function getFNumber() {
		return shell_exec($this->getExifTool() . " -EXIF:" . self::F_NUMBER . " " . $this->getFilename());
	}
	
	public function getExposureProgram() {
		$output = shell_exec($this->getExifTool() . " -EXIF:" . self::EXPOSURE_PROGRAM . " " . $this->getFilename());
		//Util::debug($output);
		return $output;
	}
	
	public function getISOSpeed() {
		return shell_exec($this->getExifTool() . " -EXIF:" . self::ISO_SPEED . " " . $this->getFilename());
	}
	
	public function getDateTimeOriginal() {
		return shell_exec($this->getExifTool() . " -EXIF:" . self::DATE_TIME_ORIGINAL . " " . $this->getFilename());
	}
	
	public function getShutterSpeed() {
		return shell_exec($this->getExifTool() . " -EXIF:" . self::SHUTTER_SPEED_VALUE . " " . $this->getFilename());
	}
	
	public function getAperture() {
		return shell_exec($this->getExifTool() . " -EXIF:" . self::APERTURE_VALUE . " " . $this->getFilename());
	}
	
	public function getBrightness() {
		return shell_exec($this->getExifTool() . " -EXIF:" . self::BRIGHTNESS_VALUE . " " . $this->getFilename());
	}
	
	public function getFlash() {
		return shell_exec($this->getExifTool() . " -EXIF:" . self::FLASH . " " . $this->getFilename());
	}
	
	public function getFocalLength() {
		return shell_exec($this->getExifTool() . " -EXIF:" . self::FOCAL_LENGTH . " " . $this->getFilename());
	}
}