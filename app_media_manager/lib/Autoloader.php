<?php
/**
 * 
 * Used in an effort to cut down on manual includes in coding.
 * Be sure that the class names match the directory pattern i.e.:
 * My_PHP_Class class must reside in the include path at My/PHP/Class.php
 * @author walshcj
 * @package
 * @name Autoloader
 *
 */
class Autoloader {
	
	/**
	 * 
	 * Automatically requires the class name.
	 * @param string $name
	 */
	public static function load($name) {
		//print("[[" . $name . "]] <br />");
		/**
		 * 
		 * The filename of the required class, by replacing undersocres with directory separators.
		 * @var string
		 */
		$filename = str_replace("_", DIRECTORY_SEPARATOR, $name);
        //echo $filename ."<br />";
		
		try {
        	/**
              * PEAR_Error class resides inside the PEAR.php file,
              * so we need to make an exception here for inclusion.
              */
              if($name == "PEAR_Error") {
              	require_once 'PEAR.php';
              } else {
                require_once $filename . ".php";
              }
		} catch(Exception $e) {
			error_log($e->getMessage(), 0);
		}
		
		return;
	}
}

/**
 * Register the auto loading class with the application
 */
spl_autoload_register("Autoloader::load");

/**
 * load up Zend framework
 */
require_once 'Zend/Loader/Autoloader.php';
?>