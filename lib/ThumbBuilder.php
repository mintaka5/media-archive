<?php
class ThumbBuidler extends Net_URL2 {
    private static $_instance;
    private $_width = null;
    private $_height = null;
//    private $_zoomCrop = null;
//    private $_quality = 75;
    private $_watermarkText = "UC Irvine";
    private $_watermarkColor = "FFFFFF";
    private $_watermarkAlpha = 20;
    private $_watermarkFont = "Arial.ttf";
//    private $_imageId = null;
    private $_settings;
    private $_filters;
    private $_id;
    
    const WATERMARK_PERCENT = 0.051;
    
    public function __construct($url, array $options = array()) {
        parent::__construct($url, $options);
        
        $this->_filters = new ArrayObject();
        
        self::$_instance = $this;
    }
    
    public static function getInstance() {
        return self::$_instance;
    }
    
    public function build($id, $jsonStr) {
        $this->setId($id);
        
        $json = new Services_JSON();
        $data = $json->decode($jsonStr);
        
        $this->setSettings($data);
        
        if(isset($this->getSettings()->w)) {
            $this->setWidth($this->getSettings()->w);
        }
        
        if(isset($this->getSettings()->h)) {
            $this->setHeight($this->getSettings()->h);
        }
        
        $this->setQueryVariable("id", $this->getId());
        
        $this->setQueryVariable("w", $this->getWidth());
        
        $this->setQueryVariable("h", $this->getHeight());
        
        if(isset($this->getSettings()->zc)) {
            $this->setQueryVariable("zc", "C");
        }
        
        if(isset($this->getSettings()->q)) {
            $this->setQueryVariable("q", $this->getSettings()->q);
        }

        if(isset($this->getSettings()->wmk)) {
        	if($this->getSettings()->wmk != false) {
        		
        		$this->setQueryVariable("fltr[]", "wmt|".$this->getWatermarkText()."|".
        				$this->getWatermarkHeight()."|*|".
        				$this->getWatermaerkColor()."|".
        				$this->getWatermaerkFont()."|".
        				$this->getWatermaerkAlpha()."|".
        				$this->getWatermarkMargin());
        	}
        }
        
        return $this->getURL();
    }
    
    private function setWidth($width) {
        $this->_width = $width;
    }
    
    private function getWidth() {
        return $this->_width;
    }
    
    private function setHeight($height) {
        $this->_height = $height;
    }
    
    private function getHeight() {
        return $this->_height;
    }
    
    private function setId($id) {
        $this->_id = $id;
    }
    
    private function getId() {
        return $this->_id;
    }
    
    public function setSettings(stdClass $settings) {
        $this->_settings = $settings;
    }
    
    private function getSettings() {
        return $this->_settings;
    }
    
    private function getWatermarkText() {
        return $this->_watermarkText;
    }
    
    private function getWatermaerkColor() {
        return $this->_watermarkColor;
    }
    
    private function getWatermaerkFont() {
        return $this->_watermarkFont;
    }
    
    private function getWatermaerkAlpha() {
        return $this->_watermarkAlpha;
    }
    
    private function addFilter(stdClass $filter) {
        $this->_filters[] = $filter;
    }
    
    private function getFilters() {
        return $this->_filters;
    }
    
    private function getWatermarkHeight() {
        return (int)$this->getHeight() * self::WATERMARK_PERCENT;
    }
    
    private function getWatermarkMargin() {
        return (int)$this->getWidth() * 0.09;
    }
    
    private function isPortrait() {
        if(($this->getWidth() / $this->getHeight() <= 0.7) && ($this->getWidth() != false)) {
            return true;
        }
        
        return false;
    }
}
?>
