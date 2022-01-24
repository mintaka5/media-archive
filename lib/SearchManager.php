<?php
class SearchManager {
	const TERMS_SESSION_NAME = "searchTerms";
	
	private static $_instance;
	
	public function __construct() {
		self::$_instance = $this;
	}
	
	public function setTerms($terms) {
		$terms = trim($terms);
		$terms = preg_replace("/[^A-Za-z0-9\s]+/", "", $terms);
		
		$_SESSION[self::TERMS_SESSION_NAME] = $terms;
	}
	
	public function getTerms($encode = false) {
		if($encode == true) {
			return urlencode($_SESSION[self::TERMS_SESSION_NAME]);
		}
		
		return $_SESSION[self::TERMS_SESSION_NAME];
	}
	
	public static function getInstance() {
		return self::$_instance;
	}
	
	public function isFromSearch() {
		$terms = $this->getTerms();
		
		if($terms != false) {
			return true;
		}
		
		return false;
	}
}