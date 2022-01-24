<?php
/**
 * Manages page requests throughout the site application
 * 
 * @author cjwalsh
 * @copyright Christopher Walsh 2010
 * @package Ode
 * @name Manager
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
 *
 */
class Ode_Manager {
	/**
	 * @var Ode_Manager
	 * @access private
	 */
	private static $instance = null;
	
	/**
	 * @var string
	 * @access private
	 */
	private $page = "index";
	
	/**
	 * @var string
	 * @access private
	 */
	private $mode = false;
	
	/**
	 * @var string
	 * @access private
	 */
	private $task = false;
	
	/**
	 * 
	 * @var string
	 * @access public
	 */
	const VAR_PAGE = "_page";
	
	/**
	 * @var string
	 * @access public
	 */
	const VAR_MODE = "_mode";
	
	/**
	 * @var string
	 * @access public
	 */
	const VAR_TASK = "_task";
	
	/**
	 * Constructor
	 * 
	 * @access public
	 * @return void
	 */
	
	private $_uri = false;
	
	public function __construct() {
		$this->setPage();
		$this->setMode();
		$this->setTask();
		
		self::$instance = $this;
		
		//Ode_Log::getInstance()->log("Done initializing Manager.", PEAR_LOG_INFO);
	}
	
	/**
	 * Sets the current page name if it is available
	 * 
	 * @param string $page
	 * @access private
	 * @return void
	 */
	private function setPage($page = null) {
		if(isset($_REQUEST[self::VAR_PAGE])) {
			$this->page = trim($_REQUEST[self::VAR_PAGE]);
		}
	}
	
	/**
	 * Retrieves the current page name.
	 * 
	 * @access public
	 * @return string
	 */
	public function getPage() {
		return $this->page;
	}
	
	/**
	 * Sets current page mode if it is available
	 * 
	 * @param string $mode
	 * @access private
	 * @return void
	 */
	private function setMode($mode = null) {
		if(isset($_REQUEST[self::VAR_MODE])) {
			$this->mode = trim($_REQUEST[self::VAR_MODE]);
		}
	}
	
	/**
	 * Retrieves current page mode
	 * 
	 * @access public
	 * @return string
	 */
	public function getMode() {
		return $this->mode;
	}
	
	/**
	 * Sets the current mode's task if it is available
	 * 
	 * @param string $task
	 * @access private
	 * @return void
	 */
	private function setTask($task = null) {
		if(isset($_REQUEST[self::VAR_TASK])) {
			$this->task = trim($_REQUEST[self::VAR_TASK]);
		}
	}
	
	/**
	 * Retrieves the current mode's task if it is available
	 * 
	 * @access public
	 * @return string
	 */
	public function getTask() {
		return $this->task;
	}
	
	public function isTask($task = false) {
		if($this->getTask() == $task) {
			return true;
		}
		
		return false;
	}
	
	public function isPage($page = false) {
		if($this->getPage() == $page) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Retrieves an instance of Ode_Manager
	 * 
	 * @access public
	 * @return Ode_Manager
	 */
	public static function getInstance() {
		return self::$instance;
	}
	
	public function action($page, $mode= null, $task = null) {
		$url = new Net_URL2($this->getURI());
		$url->setQueryVariable(self::VAR_PAGE, $page);
		
		if(!is_null($mode)) {
			$url->setQueryVariable(self::VAR_MODE, $mode);
		}
		
		if(!is_null($mode) && !is_null($task)) {
			$url->setQueryVariable(self::VAR_TASK, $task);
		}
		
		/**
		 * If the length of function arguments is more than 3
		 * then we have additional query parameters to add
		 * to the URL
		 */
                
		$args = func_get_args();
                
                $this->extraQuery($url, array_slice($args, 3));
		
		return $url->getURL();
	}
        
        private function extraQuery(Net_URL2 $url, array $args) {
            if(!empty($args)) {
                foreach($args as $arg) {
                    if(is_array($arg) || count($arg) == 2) {
                        $url->setQueryVariable($arg[0], $arg[1]);
                    }
                }
            }
        }


        public function friendlyAction($page, $mode = null, $task = null) {
		$url = new Net_URL2();
		
		$path = $this->getURI() . $page;
		
		if(!is_null($mode)) {
			$path .= "/" . $mode;
		}
		
		if(!is_null($task) && !is_null($mode)) {
			$path .= "/" . $task;
		}
		
		$url->setPath($path);
                
                $args = func_get_args();
                $this->extraQuery($url, array_slice($args, 3));
		
		return $url->getURL();
	}
	
	public function setURI($uri) {
		$this->_uri = $uri;
	}
	
	public function getURI() {
		return $this->_uri;
	}
	
	public function isMode($mode = false) {
		if($this->getMode() == $mode) {
			return true;
		}
		
		return false;
	}

    public function __toString() {
        $html = '<pre>';
        $html .= 'Page: ' . $this->getPage() . '; Mode: ' . $this->getMode() . '; Task: ' + $this->getTask();
        $html .= '</pre>';

        return $html;
    }
}