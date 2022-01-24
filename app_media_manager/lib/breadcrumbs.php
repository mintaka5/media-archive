<?php
/**
 * @author C.J. Walsh <cj@perigeeglobal.com>
 * @copyright Copyright (c) 2008, C.J. Walsh
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://www.perigeeglobal.com
 * @package BreadCrumbs
 */
class BreadCrumbs {
	/**
	 * Breadcrumb array
	 *
	 * @var array List of menu items
	 */
	var $menu = array();
	
	/**
	 * Used to append redundant URL schemes
	 *
	 * @var string
	 */
	var $url_prefix = null;
	
	/**
	 * Separator between menu items
	 *
	 * @var string Menu item separators
	 */
	var $separator = "|";
	
	/**
	 * Constructor
	 *
	 * @return BreadCrumbs
	 */
	function BreadCrumbs() {}
	
	/**
	 * Populate a link item to the menu
	 *
	 * @param string $href Anchor tag's href attribute
	 * @param string $label Anchor tag's HTML text
	 */
	function add($href = "#", $label = "*") {
		$this->menu[] = array("href" => $href, "label" => $label);
	}
	
	/**
	 * Sets the separator string for the menu
	 *
	 * @param string $str
	 */
	function setSeparator($str) {
		$this->separator = $str;
	}
	
	/**
	 * Set the URL prefix
	 *
	 * @param string $prefix
	 */
	function setUrlPrefix($prefix) {
		$this->url_prefix = $prefix;
	}
	
	/**
	 * Get URL prefix
	 *
	 * @return string
	 */
	function getUrlPrefix() {
		return $this->url_prefix;
	}
	
	/**
	 * Retrieve default separator
	 *
	 * @return string
	 */
	function getSeparator() {
		return $this->separator;
	}
	
	/**
	 * Output menu array as HTML
	 *
	 * @return string HTML string
	 */
	function toHtml() {
		$htmlary = array();
		
		foreach($this->menu as $k => $v) {
			$htmlary[] = "<a href=\"" . $this->getUrlPrefix() .$v['href']."\">".$v['label']."</a>";
		}
		
		return implode($this->getSeparator(), $htmlary);
	}
}
?>