<?php
/**
 * external PHP libraries
 */
@define('APP_PEAR_PATH', "/usr/share/pear");
@define('APP_ZEND_PATH', "/data/webserv/zend/library");
/**
 * site database settings
*/
@define('APP_DB_NAME', 'dev-image-archive');
@define('APP_DB_HOST_SPEC', 'cwisdb2.cwis.uci.edu');
@define('APP_DB_USER', 'image_archive');
@define('APP_DB_PASSWD', '1m@g3_r39o');

/**
 * site URI settings
*/
@define('APP_DOMAIN', 'dev.images.communications.uci.edu');
@define('REL_URL', '/');

// Define path for Imagemagick
@define("APP_IMAGEMAGICK_PATH", "/usr/bin");

// log file
@define("APP_ERROR_LOG", "/data/logs/php_error.log");

// default image storage path
@define("IMAGE_STORAGE_PATH", DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "storage" . DIRECTORY_SEPARATOR);

//
@define('APP_UPLOAD_TMP_PATH', "/data/webserv/temp/uploads");
@define('APP_CACHE_PATH', "/data/webserv/temp/cache");

// EXIF/XMP Perl tool
@define("EXIFTOOL_PATH", "/usr/bin/exiftool");

$display_errors = true;
$log_errors = true;
?>
