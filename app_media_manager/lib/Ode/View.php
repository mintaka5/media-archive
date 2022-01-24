<?php
/**
 * View.php
 * 
 * Template creator and decision-maker
 * 
 * @author cjwalsh
 * @copyright Christopher Walsh 2011
 * @version 1.0
 * @package Ode
 * @name View
 * @license This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

 * @todo May have to move this out, and add the view instance to the controller one
 *
 */
class Ode_View extends Savant3 {
	/**
	 * A reference to this instance
	 * 
	 * @var Ode_View
	 * @access private
	 */
	private static $instance = null;
	
	/**
	 * Path on server to template files
	 * 
	 * @var string
	 * @access private
	 */
	private $path = null;
	
	protected $manager = null;
	
	/**
	 * @see Savant3 documentation
	 * 
	 * @var string
	 * @access private
	 */
	private $type = "template";
	
	/**
	 * 
	 * @var string
	 * @access private
	 */
	private $layout = false;
	
	/**
	 * Template default extension
	 * 
	 * @var string
	 * @access public
	 */
	const TEMPLATE_EXT = ".tpl.php";
	
	private $_formRenderer = false;
	
	const FORM_ELEMENT_TEMPLATE = "form/element/template.tpl.php";
	
	const FORM_FIELDSET_CLOSE_TEMPLATE = "form/fieldset/close.tpl.php";
	
	const FORM_FIELDSET_OPEN_TEMPLATE = "form/fieldset/open.tpl.php";
	
	const FORM_FIELDSET_HIDDEN_TEMPLATE = "form/fieldset/hidden/open.tpl.php";
	
	const FORM_HEADER_TEMPLATE = "form/header/template.tpl.php";
	
	private $_fileCreation = false;
	
	/**
	 * Retrieve an insance of this class
	 * 
	 * @return Ode_View
	 * @access public
	 */
	public static function getInstance() {
		return self::$instance;
	}
	
	/**
	 * Set the instance reference of this class
	 * 
	 * @param Savant3 $view
	 * @return void
	 * @access private
	 */
	private function setInstance(Savant3 $view) {
		self::$instance = $view;
	}
	
	/**
	 * Constructor
	 * 
	 * @param string $path
	 * @return void
	 * @access public
	 */
	public function __construct($path, $ajax = false) {
		$this->setPath($this->getType(), $path);
		
		self::$instance = $this;
		
		$this->setManager();
		
		$this->setAuth();
		
		$this->setFormRenderer();
		
		if($ajax === false) {
			Ode_Controller::getInstance()->init($this);
			
			$this->setLayout("layout" . self::TEMPLATE_EXT);
		}
	}
	
	/**
	 * 
	 * Sets the form renderer for HTML_QuickForm2
	 * @access private
	 * @return void
	 */
	private function setFormRenderer() {
		$this->_formRenderer = HTML_QuickForm2_Renderer::factory("default");
	}
	
	public function getFormRenderer() {
		return $this->_formRenderer;
	}
	
	/**
	 * 
	 * @param string $name
	 * @access private
	 */
	private function setLayout($name) {
		$this->layout = $name;
	}
	
	/**
	 * 
	 * @return string
	 * @access public
	 */
	public function getLayout() {
		return $this->layout;
	}
	
	/**
	 * Creates and assigns content to the view instance
	 * 
	 * @return void
	 * @access public
	 */
	public function setContentTemplate() {
		if(!$this->templateExists(Ode_Manager::getInstance()->getPage() . self::TEMPLATE_EXT)) {
			/**
			 * @todo figure out a way to get the template path without using config variable
			 */
			if($this->createFiles() == true) {
				File::writeLine(APP_VIEW_PATH . DIRECTORY_SEPARATOR . Ode_Manager::getInstance()->getPage() . self::TEMPLATE_EXT, '<div><?php echo $this->controllerName; ?></div>', FILE_MODE_WRITE);
			}
		}
		
		self::getInstance()->assign("contentforlayout", self::getInstance()->fetch(Ode_Manager::getInstance()->getPage() . self::TEMPLATE_EXT));
	}
	
	private function createFiles() {
		return $this->_fileCreation;
	}
	
	public function setFileCreation($bool = true) {
		$this->_fileCreation = $bool;
	}
	
	/**
	 * Retrieve the type of view (template/resource)
	 * 
	 * @see Savant3 documentation
	 * @return string
	 * @access private
	 */
	private function getType() {
		return $this->type;
	}
	
	/**
	 * Set the type of view (template/resource)
	 * 
	 * @see Savant3 documentation
	 * @param string $type
	 */
	private function setType($type) {
		$this->type = $type;
	}
	
	/**
	 * Looks for the content template file
	 * within the specified template paths
	 * 
	 * @param string $filename
	 * @return string/boolean
	 */
	private function templateExists($filename) {
		$paths = self::getInstance()->getConfig("template_path");
		foreach($paths as $path) {
			if(file_exists($path . $filename)) {
				return $path;
			}
		}
		
		return false;
	}
	
	private function setManager(Ode_Manager $manager = null) {
		if(!is_null($manager)) {
			$this->manager = $manager;
		} else {
			$this->manager = Ode_Manager::getInstance();
		}
		
		self::$instance->assign("manager", $this->getManager());
	}
	
	private function getManager() {
		return $this->manager;
	}
	
	/**
	 * View-based date formatter
	 * @param mixed $date
	 * @param string $format
	 * @return string|string|boolean
	 */
	public function date($date, $format = "m/d/Y") {
		if(is_int($date) || is_float($date)) {
			return date($format, $date);
		} else if(is_string($date)) {
			return date($format, strtotime($date));
		} else {
			return false;
		}
	}
	
	/**
	 * 
	 * Truncates a string to a specific word length
	 * and adds a specified ending string
	 * @param string $str
	 * @param integer $limit
	 * @param string $tail
	 */
	public function truncate($str, $limit = 10, $tail = "&#133;") {
                // get rid of HTML
		$str = strip_tags($str);
                // split apart words
		$orig = preg_split("/[\s\t\r\n]+/", $str);
                // truncate words down to limit
		$new = array_slice($orig, 0, $limit, true);
		
		if(count(($new) > 1) && (strlen($str) <= $limit)) { // more than 1 word, and phrase's string length is shorter than limit
                    if(count($orig) <= count($new)) {
                        return $str;
                    } else {
                        return implode(" ", $new) . $tail;
                    }
		} else if((count($new) > 1) && (strlen($str) > $limit)) { // make sure length of phrase's string limit is no longer than limit
                    return $this->truncateChars($str, $limit, $tail);
                } else {
                    return $this->truncateChars($str, $limit, $tail);
		}
	}
	
	/**
	 * Truncates string per characters
	 * @param string $str
	 * @param integer $limit
	 * @param string $tail
	 * @return string
	 */
	public function truncateChars($str, $limit = 10, $tail = "&#133;") {
		$str = strip_tags($str);
		
		if(strlen($str) <= $limit) {
			return $str;
		} else {
			return substr($str, 0, ($limit-1)) . $tail;
		}
	}
	
	/**
	 * 
	 * Converts binary true/false 0/1 to a specified text equivalent
	 * @param integer $binary
	 * @param string... The two following arguments should be the 0/1 
	 * 	text equivalents in numerical order (i.e. "No...", "Yes...")
	 */
	public function binaryToText($binary) {
		$defaults = array("No", "Yes");
		$args = func_get_args();
		if(!empty($args[1])) {
			$defaults[0] = $args[1];
		}
		
		if(!empty($args[2])) {
			$defaults[1] = $args[2];
		}
		
		$truth = ($binary == 0 || $binary == false) ? 0 : 1;
		
		return ($truth === 0) ? $defaults[0] : $defaults[1];
	}
	
	public function isIE() {
		$userAgent = new Net_UserAgent_Detect();
		
		if(stristr($userAgent->getBrowserString(), "internet explorer")) {
			return true;
		}
		
		return false;
	}
	
	public function linkAlternate($href, $label, $is_link = true, $title = '') {
		$str = "\n";
		
		if($is_link == true) {
			$str .= '<a href="' . $href . '" title="' . $title . '">';
		}
		
		$str .= $label;
		
		if($is_link == true) {
			$str .= "</a>\n";
		}
		
		return $str;
	}
}