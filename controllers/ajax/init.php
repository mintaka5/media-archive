<?php
require_once dirname(dirname(dirname(__FILE__))) . '/config.php';

ini_set("magic_quotes_gpc", false);

set_include_path(
	'../..' . PATH_SEPARATOR . 
	APP_INC_PATH . PATH_SEPARATOR . 
	APP_PEAR_PATH . PATH_SEPARATOR .
	APP_ZEND_PATH
);

require_once 'Autoloader.php';

$log = new Ode_Log(APP_ERROR_LOG);
$log->setIdent("UCI");

$auth = new Auth();

@session_start();

$dbo = new Ode_DBO(APP_DB_HOST_SPEC, APP_DB_NAME, APP_DB_USER, APP_DB_PASSWD);

$manager = new Ode_Manager();
$manager->setURI(REL_URL);

$order = new Order(Ode_Auth::getInstance()->getSession()->id);

require_once 'ThumbBuilder.php';
$thumber = new ThumbBuidler($manager->getURI()."imggen.php");

require_once 'AssetManager.php';
$asset_manager = new AssetManager();

$view = new Ode_View(APP_VIEW_PATH, true);
$view->assign("auth", $auth);
$view->assign("thumber", $thumber);
$view->assign("assetmanager", $asset_manager);
$view->setFileCreation(false);
?>