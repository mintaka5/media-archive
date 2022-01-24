<?php
/**
 * site file system settings
 */
@define('APP_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
@define('APP_INC_PATH', APP_PATH . 'lib' . DIRECTORY_SEPARATOR);

@define('APP_LIVE_SERVER_ADDR', "127.0.0.1");
@define('APP_DEV_SERVER_ADDR', "");

if($_SERVER['SERVER_ADDR'] == APP_LIVE_SERVER_ADDR) { // LIVE SERVER DATABASE SETTINGS
	require_once 'configs/production.php';
} else if($_SERVER['SERVER_ADDR'] == APP_DEV_SERVER_ADDR) { // DEV SERVER DATABASE SETTINGS
	require_once 'configs/dev.php';
} else {
	die("Please, provide valid server settings for " . $_SERVER['SERVER_ADDR'] . "!");
}

// default extensions
@define('PHP_EXT', '.php');
@define('TEMPLATE_EXT', '.tpl.php');

@define('BASE_URL', 'http://' . APP_DOMAIN . REL_URL);

// encryption key
@define("APP_ENC_KEY", "this-is-uci");

// view template path
@define("APP_VIEW_PATH", APP_PATH . "views");

// phpThumb's max width
@define("PT_MAX_COPY_WIDTH", 200);

// temporary directory for image reading/writing
//@define("APP_CACHE_DIR", APP_PATH . "cache");

@define("SESSION_UPLOAD_NAME", "uploads");

/**
 * A place to store shopping cart ID #
 */
@define("SESSION_ORDER_NAME", "orderID");

@define("FAQ_DATABASE_NAME", "phpmyfaq");

@define("DEFAULT_UCI_COPYRIGHT", "University of California, Irvine");

@define("CURRENT_GROUP_VAR", "currentGroup");

/**
 * Settings for deciding whether we are using the default auth system,
 * or something else (preferably WebAuth)
 */
@define("WEB_AUTH", 1);
@define("DEFAULT_AUTH", 0);

@define("APP_ADMIN_EMAIL", "Chris Walsh <walshcj@uci.edu>");

/**
 * Metadata settings
 */
@define("META_COPYRIGHT_NAME", "copyright");

/**
 * OpenStreetMap geocode URL
 */
@define("OSM_SEARCH_URL", "http://nominatim.openstreetmap.org/search/");

ini_set("upload_tmp_dir", APP_UPLOAD_TMP_PATH);
ini_set("display_errors", $display_errors);
ini_set("log_errors", $log_errors);
ini_set("error_log", APP_ERROR_LOG);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
?>
