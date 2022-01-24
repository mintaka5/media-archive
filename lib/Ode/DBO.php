<?php
/**
 * Database object
 * 
 * @author cjwalsh
 * @copyright Christoper Walsh 2010
 * @package Ode
 * @name DBO
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
class Ode_DBO extends PDO {
	/**
	 * 
	 * @var Ode_DBO
	 * @access private
	 */
	private static $instance = null;
	
	/**
	 * 
	 * @var string
	 * @access private
	 */
	private $username;
	
	/**
	 * 
	 * @var string
	 * @access private
	 */
	private $passwd;
	
	/**
	 * Databse schema name
	 * 
	 * @var string
	 * @access private
	 */
	private $name;
	
	/**
	 * 
	 * @var string
	 * @access private
	 */
	private $host;
	
	/**
	 * Default date/time format for MySQL
	 * @var string
	 * @access public
	 */
	const DATETIME_FORMAT = "%Y-%m-%d %H:%M:%S";
	
	/**
	 * Constructor
	 * 
	 * @param string $hostspec Database host URI
	 * @param string $dbname Database schema name
	 * @param string $user Username
	 * @param string $passwd Password
	 * @access public
	 * @return void
	 */
	public function __construct($hostspec, $dbname, $user, $passwd) {
		$this->setHost($hostspec);
		$this->setName($dbname);
		$this->setUsername($user);
		$this->setPassword($passwd);
		
		try {
			parent::__construct("mysql:host=" . $this->getHost() . ";dbname=" . $this->getName(), $this->getUsername(), $this->getPassword());
			parent::setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
		} catch (PDOException $e) {
            error_log($e->getMessage(), 0);
        }
		
		//Ode_Log::getInstance()->log("Initializing DBO instance", PEAR_LOG_INFO);
		
		self::$instance = $this;
	}
	
	/**
	 * Sets database username
	 * 
	 * @param string $username
	 * @access protected
	 * @return void
	 */
	protected function setUsername($username) {
		$this->username = $username;
	}
	
	/**
	 * Sets database password
	 * 
	 * @param string $passwd
	 * @access protected
	 * @return void
	 */
	protected function setPassword($passwd) {
		$this->passwd = $passwd;
	}
	
	/**
	 * Sets database schema name
	 * 
	 * @param string $name
	 * @access protected
	 * @return void
	 */
	protected function setName($name) {
		$this->name = $name;
	}
	
	/**
	 * Sets database host URI
	 * 
	 * @param string $host
	 * @access protected
	 * @return void
	 */
	protected function setHost($host) {
		$this->host = $host;
	}
	
	/**
	 * Retrieves database username
	 * 
	 * @access protected
	 * @return string
	 */
	protected function getUsername() {
		return $this->username;
	}
	
	/**
	 * Retrieves database password
	 * 
	 * @access protected
	 * @return string
	 */
	protected function getPassword() {
		return $this->passwd;
	}
	
	/**
	 * Retrieves database schema name
	 * 
	 * @access protected
	 * @return string
	 */
	protected function getName() {
		return $this->name;
	}
	
	/**
	 * Retrieves database host URI
	 * 
	 * @access protected
	 * @return string
	 */
	protected function getHost() {
		return $this->host;
	}
	
	/**
	 * Retrieves Ode_DBO instance
	 * 
	 * @access public
	 * @return Ode_DBO
	 */
	public static function getInstance() {
		return self::$instance;
	}
	
	public static function formatDate($dateStr) {
		$date = new Date(strtotime($dateStr));
		
		return $date->format(self::DATETIME_FORMAT);
	}
}