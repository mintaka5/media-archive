<?php
/**
 * @author C.J. Walsh <cj@perigeeglobal.com>
 * @copyright Copyright (c) 2008, C.J. Walsh
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://www.perigeeglobal.com
 * @package Misc
 * @abstract
 */
class Misc {
    
        public static function featuredGroupsCapped($property_name, $limit) {
            $property = Ode_DBO::getInstance()->query("
                SELECT a.value
                FROM " . DBO_Properties::TABLE_NAME . " AS a
                WHERE a.machine_name = '" . $property_name . "'
                LIMIT 0,1
            ")->fetchColumn();
            $json = new Services_JSON();
            
            if(!empty($property)) {
                $featured_sets = $json->decode($property);
                $num = count($featured_sets);
                if($num >= $limit) {
                    return true;
                }
            }
            
            return false;
        }
    
	/**
	 * Convert array to select options
	 * array
	 *
	 * @param array $list Associative array of data
	 * @param string $valueFld Column name to be used as option value
	 * @param mixed $displayFld Column name(s) to be used as option HTML text
	 * @param string $format How to format the string (i.e. "%s - %s")
	 * @param boolean $useSelect Whether to add an - select - option
	 * @return array
	 */
	function optionize($list, $valueFld, $displayFld, $format = null, $useSelect = false) {
		if($useSelect) {
			$ary[''] = '- select -';
		}
		
		foreach($list as $k => $v) {
			if(is_array($displayFld) && !is_null($format)) { // we're using multiple columns in a single string format
				$display_vals = array();
				foreach($displayFld as $l => $w) {
					$display_vals[] = $v[$w];
				}
				
				$ary[$v[$valueFld]] = vsprintf($format, $display_vals);
			} else if(is_string($displayFld)) {
				$ary[$v[$valueFld]] = $v[$displayFld];
			}
			
		}
		
		return $ary;
	}
	
	/**
	 * Search for a value inside of
	 * multidimensional array
	 *
	 * @param string $needle
	 * @param array $haystack
	 * @param string $key
	 * @return boolean Return key
	 */
	function multi_array_search($needle, $haystack, $key) {
		foreach($haystack as $k => $v) {
			if($haystack[$k][$key] == $needle) {
				return $k;
				exit();
			}
		}
		
		return false;
	}
	
	/**
	 * Retrieve states and provinces
	 *
	 * @return array
	 */
	function getZones() {
		global $db;
	
		$sql = "SELECT 
					zones.zone_code AS abbr, 
					zones.zone_name AS state
				FROM ".DB_TBL_PREFIX."zones AS zones
				LEFT JOIN " . DB_TBL_PREFIX . "countries AS countries
				ON (countries.countries_id = zones.zone_country_id)
				ORDER BY zones.zone_name
				ASC";
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	function getCountries() {
		global $db;
		
		$sql = "SELECT
					countries.countries_id AS id,
					countries.countries_name AS country
				FROM " . DB_TBL_PREFIX . "countries AS countries
				ORDER BY countries_name
				ASC";
		
		$res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::isError($res)) trigger_error($res->getDebugInfo(), E_USER_ERROR);
		
		return $res;
	}
	
	/**
	 * Convert array keys to corresponding values
	 *
	 * @param array $list Associative data array
	 * @return array
	 */
	function value2Key($list) {
		$ary = array();
		foreach($list as $v) {
			$ary[$v] = $v;
		}
		
		return $ary;
	}
	
	/**
	 * Output an associative array of data
	 * to HTML options
	 *
	 * @param array $data
	 * @param string $val Option value
	 * @param mixed $lbl Option label/inner HTML
	 * @param string $format Format of the label/inner HTML
	 * @param boolean $use_select Show an empty value '- select -'
	 * @param mixed $sel Default value
	 * @return string HTML
	 */
	function optionsToHtml($data, $val, $lbl, $format = null, $use_select = false, $sel = "") {
		$set = Misc::optionize($data, $val, $lbl, $format, $use_select);
		
		$html = "\t\n";
		
		if(!empty($set)) {
			foreach($set as $k => $v) {
				$html .= "<option value=\"". $k ."\"";
				if($k == $sel) {
					$html .= " selected=\"selected\"";
				}
				$html .= ">" . $v . "</option>\n\t";
			}
		}
		
		return $html;
	}
	
	/**
	 * Since we strip all characters out of numbers
	 * for normalization, we need to digest data 
	 * to and from the database.
	 *
	 * @param string $phone Phone number string
	 * @param boolean $fromdb Is string coming from a database column
	 * @return string Formatted phone number
	 */
	function formatPhone($phone, $fromdb = false) {
		if(is_null($phone) || empty($phone)) {
			return false;
		}
	
		if($fromdb) {
			return "(" . substr($phone, 0, 3) . ") " . substr($phone, 3, 3) . "-" . substr($phone, 6, 4);
		} else {
			// to the database
			return preg_replace("#[\-\(\)\+\s\t\n\r]+#", "", $phone);
		}
	}
	
	/**
	 * Recursively reduces deep arrays to single-dimensional arrays
	 * $preserve_keys: (0=>never, 1=>strings, 2=>always)
	 *
	 * @param array $array
	 * @param integer $preserve_keys
	 * @param array $newArray
	 * @return array
	 */
	function array_flatten($array, $preserve_keys = 1, &$newArray = Array()) {
		foreach ($array as $key => $child) {
			if (is_array($child)) {
	  			$newArray =& array_flatten($child, $preserve_keys, $newArray);
			} elseif ($preserve_keys + is_string($key) > 1) {
	  			$newArray[$key] = $child;
			} else {
	  			$newArray[] = $child;
			}
		}
		
		return $newArray;
	}
	
	function getLastID() {
		global $db;
		
		$sql = "SELECT LAST_INSERT_ID();";
		$res = $db->getOne($sql);
		
		return $res;
	}
	
	function goBack($url = null) {
		$referer = is_null($url) ? $_SERVER['HTTP_REFERER'] : $url;
		
		if(strpos($referer, APP_DOMAIN)) {
			header("Location: " . substr($referer, strpos($referer, REL_URL)));
			exit();
		}
	}
	
	function generate_passwd($length = 16) {
  		static $chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ23456789';
  		
  		$chars_len = strlen($chars);
  		
  		for ($i = 0; $i < $length; $i++) {
    		$password .= $chars[mt_rand(0, $chars_len - 1)];
  		}
  		
  		return $password;
	}
	
	function truncate($str, $length = 16, $suffix = '...') {
		$newstr = '';
		
		if(!empty($str) || ($str != '')) {
			if(strlen($str) > $length) {
				$newstr = substr($str, 0, $length) . $suffix;
			} else {
				$newstr = $str;
			}
			
		}
		
		return $newstr;
	}
	
	function debug($data, $dump = false) {
		print('<pre>');
		if($dump) {
			var_dump($data);
		} else {
			print_r($data);
		}
		print('</pre>');
	}
	
	function validateMultipleInputs($data) {
		$bool = true;
		
		foreach($data as $item) {
			if(empty($item) || $item == "") {
				$bool = false;
				break;
			}
		}
		
		return $bool;
	}
}
?>