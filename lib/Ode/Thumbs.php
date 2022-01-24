<?php
require_once 'phpThumb-1.7.11/phpthumb.class.php';

class Ode_Thumbs extends phpthumb {	
	const WATERMARK_PERCENT = 0.051;
	const WATERMARK_FONT = "Arial.ttf";
	const WATERMARK_MARGIN_PERCENT = 0.09;
	
	private $width = 100;
	private $height = 100;
	
	private $filters = array();
	
	private $watermarkSettings = array();
	
	public function __construct() {
		parent::phpThumb();
		
		$this->config_output_format = 'jpeg';
		$this->config_nohotlink_enabled = true;
		$this->config_nooffsitelink_enabled = true;
		$this->config_prefer_imagemagick = true;
		$this->config_error_die_on_error = true;
		/**
		 * @todo Change this to true
		 * @var boolean
		 */
		$this->config_disable_debug = false;
		$this->config_document_root = '/';
	}
	
	/**
	 * 
	 * @param string $public_id Asset's public_id field
	 */
	public function setSourceFromPublicId($public_id) {
		$asset = DBO_Asset::getOneByPublicId($public_id);
		
		if($asset != false) {
			$filename = IMAGE_STORAGE_PATH . $asset->filename;
			
			$this->setSourceFilename($filename);
		}
	}
	
	public function generate($w = false, $h = false, $zc = false, $q = false) {
		if($w) {
			$this->width = $w;
			
			$this->setParameter('w', $w);
		}
		
		if($h) {
			$this->height = $h;
			
			$this->setParameter('h', $h);
		}
		
		if($zc) {
			$this->setParameter('zc', $zc);
		}
		
		if($q) {
			$this->setParameter('q', $q);
		}
		
		if(!empty($this->watermarkSettings)) {
			$this->setWatermark($this->watermarkSettings[1], $this->watermarkSettings[6], $this->watermarkSettings[3]);
			
			$this->filters[] = implode("|", $this->watermarkSettings);
		}
		
		if(!empty($this->filters)) {
			$this->setParameter('fltr', $this->filters);
		}
		
		if($this->GenerateThumbnail()) {
			$this->OutputThumbnail();
		}
	}
	
	public function setWatermark($text = 'WATERMARK', $opacity = 20, $layout = "*") {
		$this->watermarkSettings = array(
				"wmt", 
				$text, 
				(int)(self::WATERMARK_PERCENT * $this->width), 
				$layout, 
				"FFFFFF", 
				self::WATERMARK_FONT, 
				$opacity, 
				(int)($this->height * self::WATERMARK_MARGIN_PERCENT)
		);
	}
}
?>