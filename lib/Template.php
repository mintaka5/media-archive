<?php
class Template extends Savant3 {
	private static $_instance;
	
	public function __construct() {
		parent::__construct();
		
		self::$_instance = $this;
	}
	
	public static function getInstance() {
		return self::$_instance;
	}
}