<?php
/**
 * external PHP libraries
 */
@define('APP_PEAR_PATH', '/home/cj5/lib/php/pear/pear/php');
@define('APP_ZEND_PATH', '/home/cj5/lib/php/zend/library');

/**
 * site database settings
*/
@define('APP_DB_NAME', 'db_media_manager');
@define('APP_DB_HOST_SPEC', 'localhost');
@define('APP_DB_USER', 'image_archive');
@define('APP_DB_PASSWD', 'vf3NP1zrEV8eMUEc');

/**
 * site URI settings
*/
@define('APP_DOMAIN', 'mm.cj5.webfactional.com');
@define('REL_URL', '/');

// Define path for Imagemagick
@define("APP_IMAGEMAGICK_PATH", '/usr/bin');

// log file
@define("APP_ERROR_LOG", '/home/cj5/data/logs/php_errors.log');

// default image storage path
@define('IMAGE_STORAGE_PATH',  '/home/cj5/data/media/storage/');

// EXIF/XMP Perl tool
@define('EXIFTOOL_PATH', '/home/cj5/Image-ExifTool-9.97/exiftool');

@define('APP_UPLOAD_TMP_PATH', '/home/cj5/data/tmp/uploads');
@define('APP_CACHE_PATH', '/home/cj5/data/tmp/cache');

$display_errors = false;
$log_errors = true;
?>
