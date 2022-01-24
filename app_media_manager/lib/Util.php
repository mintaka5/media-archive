<?php
/**
 * Miscellaneous utility functions
 * 
 * @author cjwalsh
 * @copyright Christopher Walsh 2010
 * @package
 * @name Util
 *
 */
class Util {
	const CHECKED_TEXT = 'checked="checked"';
	
	/**
	 * Debug output
	 * 
	 * @param mixed $data
	 * @param boolean $dump
	 * @access public
	 */
	public static function debug($data, $dump = true) {
		print "<pre>";
		if($dump == true) {
			var_dump($data);
		} else {
			print_r($data);
		}
		print "</pre>";
	}
	
	public static function json($data) {
		$ary = array("status" => true, "data" => $data);
		
		if(!$data) {
			$ary = array("status" => false, "data" => "");
		}
		
		header("Content-Type: application/json");
		if(class_exists('Services_JSON')) {
			$json = new Services_JSON();
			echo $json->encode($ary);
		} else {
			echo json_encode($ary);
		}
		
		return;
	}
	
	/**
	 * Replaces any parameter placeholders in a query with the value of that
	 * parameter. Useful for debugging. Assumes anonymous parameters from 
	 * $params are are in the same order as specified in $query
	 *
	 * @param string $query The sql query with parameter placeholders
	 * @param array $params The array of substitution parameters
	 * @return string The interpolated query
	 */
	public static function interpolateQuery($query, $params) {
	    $keys = array();
	
	    // build a regular expression for each parameter
	    foreach ($params as $key => $value) {
	        if (is_string($key)) {
	            $keys[] = '/:'.$key.'/';
	        } else {
	            $keys[] = '/[?]/';
	        }
	    }
	
	    $query = preg_replace($keys, $params, $query, 1, $count);

	    //trigger_error('replaced '.$count.' keys');

	    return $query;
	}
	
	/**
	 * 
 	 *
 	 * If you want the alphaID to be at least 3 letter long, use the
 	 * $pad_up = 3 argument
 	 *
 	 * In most cases this is better than totally random ID generators
 	 * because this can easily avoid duplicate ID's.
 	 * For example if you correlate the alpha ID to an auto incrementing ID
 	 * in your database, you're done.
 	 *
 	 * The reverse is done because it makes it slightly more cryptic,
 	 * but it also makes it easier to spread lots of IDs in different
 	 * directories on your filesystem. Example:
 	 * $part1 = substr($alpha_id,0,1);
 	 * $part2 = substr($alpha_id,1,1);
 	 * $part3 = substr($alpha_id,2,strlen($alpha_id));
 	 * $destindir = "/".$part1."/".$part2."/".$part3;
 	 * by reversing, directories are more evenly spread out. The
 	 * first 26 directories already occupy 26 main levels

	 * @param string $in
	 * @param boolean $to_num
	 * @param boolean $pad_up
	 * @param string $passKey
	 */
	function alphaID($in, $to_num = false, $pad_up = false, $passKey = null) {
		$index = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		if ($passKey !== null) {
			// Although this function's purpose is to just make the
			// ID short - and not so much secure,
			// with this patch by Simon Franz (http://blog.snaky.org/)
			// you can optionally supply a password to make it harder
			// to calculate the corresponding numeric ID
	
			for ($n = 0; $n<strlen($index); $n++) {
				$i[] = substr( $index,$n ,1);
			}
	
			$passhash = hash('sha256',$passKey);
			$passhash = (strlen($passhash) < strlen($index)) ? hash('sha512',$passKey) : $passhash;
	
			for ($n=0; $n < strlen($index); $n++) {
				$p[] =  substr($passhash, $n ,1);
			}
	
			array_multisort($p,  SORT_DESC, $i);
			$index = implode($i);
		}
	
		$base  = strlen($index);
	
		if ($to_num) {
			// Digital number  <<--  alphabet letter code
			$in  = strrev($in);
			$out = 0;
			$len = strlen($in) - 1;
			for ($t = 0; $t <= $len; $t++) {
				$bcpow = bcpow($base, $len - $t);
				$out   = $out + strpos($index, substr($in, $t, 1)) * $bcpow;
			}
	
			if (is_numeric($pad_up)) {
				$pad_up--;
				if ($pad_up > 0) {
					$out -= pow($base, $pad_up);
				}
			}
			
			$out = sprintf('%F', $out);
			$out = substr($out, 0, strpos($out, '.'));
		} else {
			// Digital number  -->  alphabet letter code
			if (is_numeric($pad_up)) {
				$pad_up--;
				if ($pad_up > 0) {
					$in += pow($base, $pad_up);
				}
			}
	
			$out = "";
			for ($t = floor(log($in, $base)); $t >= 0; $t--) {
				$bcp = bcpow($base, $t);
				$a   = floor($in / $bcp) % $base;
				$out = $out . substr($index, $a, 1);
	      		$in  = $in - ($a * $bcp);
	    	}
	    	
	    	$out = strrev($out); // reverse
		}
		
		return $out;
	}
	
	public static function randomString() {
		return md5(uniqid(rand(), true));
	}
	
	public static function dbQuoteListItems($array, $param_type = PDO::PARAM_STR) {
		$new = array();
		
		if(!empty($array)) {
			foreach($array as $item) {
				$new[] = Ode_DBO::getInstance()->quote($item, $param_type);
			}
			
			return $new;
		}
		
		return false;
	}
	
	public static function filesize($path) {
	    $bytes = sprintf('%u', filesize($path));
	
	    if ($bytes > 0)
	    {
	        $unit = intval(log($bytes, 1024));
	        $units = array('B', 'KB', 'MB', 'GB');
	
	        if (array_key_exists($unit, $units) === true)
	        {
	            return sprintf('%d %s', $bytes / pow(1024, $unit), $units[$unit]);
	        }
	    }
	
	    return $bytes;
	}
	
	public static function simpleID() {
		exec('echo $(openssl rand -base64 6 | tr -cd "[:alnum:]")', $output);
		$simpleID = strtoupper($output[0]);
		
		return $simpleID;
	}
	
	public static function encrypt($key, $str) {
		return base64_encode(mcrypt_encrypt(MCRYPT_3DES, md5($key), $str, MCRYPT_MODE_CBC, md5(md5($key))));
	}
	
	public static function decrypt($key, $enc_str) {
		return rtrim(mcrypt_decrypt(MCRYPT_3DES, md5($key), base64_decode($enc_str), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
	}
	
	public static function stringify($data) {
		$str = "";
		
		if(is_array($data)) {
			$str = implode(" ", $data);
		} else if(is_string($data)) {
			$str = $data;
		}
		
		return $str;
	}
	
	public static function imageDimensions($filename) {
		$obj = new stdClass();
		
		$dimensions = getimagesize($filename);
		
		$obj->width = new stdClass();
		$obj->width->pixels = $dimensions[0];
		$obj->height = new stdClass();
		$obj->height->pixels = $dimensions[1];
		
		$meta = new Metadata_XMP($filename);
		$resolution = (is_object($meta->dpi())) ? $meta->dpi()->resolution : "?";
		$widInches = ($resolution > 0) ? round((int)$dimensions[0] / (int)$resolution, 1) : "?";
		$heiInches = ($resolution > 0) ? round((int)$dimensions[1] / (int)$resolution, 1) : "?";
		$obj->width->inches = $widInches;
		$obj->height->inches = $heiInches;
		
		$obj->resolution = $resolution;

		return $obj;
	}
}