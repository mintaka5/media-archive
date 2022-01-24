<?php
/**
 * 
 * Generic class for managing site authorization
 * @author walshcj
 * @copyright Christopher Walsh 2011
 * @package Ode
 * @name Auth
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
class Ode_Auth
{
	private $sessName = "_user";
	
	private $_referer;
	
	private static $_instance = false;
	
	public function __construct()
	{
		self::$_instance = $this;
	}
	
	public static function getInstance() {
		return self::$_instance;
	}
	
	public function setSessName($v)
	{
		$this->sessName = $v;
	}
	
	public function getSessName()
	{
		return $this->sessName;
	}
	
	public function isAuth()
	{
		if(isset($_SESSION[$this->getSessName()])) {
			return true;
		}
		
		return false;
	}
	
	public function setSession($data) {
		$_SESSION[$this->getSessName()] = $data;
	}
	
	public function killSession() {
		unset($_SESSION[$this->getSessName()]);
	}
	
	public function getSession() {
		if(isset($_SESSION[$this->getSessName()])) {
			return $_SESSION[$this->getSessName()];
		}
		
		return false;
	}
	
	public function hasSession() {
		if(!$this->getSession()) {
			return false;
		}
		
		return true;
	}
}
?>