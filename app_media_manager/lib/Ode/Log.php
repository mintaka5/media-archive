<?php
/**
 * Application-wide logging handler
 * 
 * @author cjwalsh
 * @copyright Christopher Walsh 2011
 * @package Ode
 * @name Log
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
class Ode_Log extends Log {
	/**
	 * 
	 * @var Ode_Log
	 * @access private
	 */
	private static $instance = null;
	
	/**
	 * Constructor
	 * 
	 * @param string $filename
	 * @param string $ident
	 * @access public
	 * @return void
	 */
	public function __construct($filename, $ident = "ODEAPP") {
		if(!file_exists($filename)) {
			touch($filename);
		}
		
		self::$instance = self::factory("file", $filename, $ident, array(
			"mode" => 0666,
			"append" => false,
			"timeFormat" => "%Y-%m-%d %H:%M:%S",
			"lineFormat" => "%{ident} | %{timestamp} | msg:%{message} | file:%{file} | line:%{line} | function:%{function} | class:%{class}"
		));
		
		//self::getInstance()->log("Initializing site log.", PEAR_LOG_INFO);
	}
	
	/**
	 * Retrieves Log instance
	 * 
	 * @access public
	 * @return Ode_Log
	 */
	public static function getInstance() {
		return self::$instance;
	}
}