<?php
require_once 'config.php';

require_once 'php_settings.php';

require_once 'Autoloader.php';

$log = new Ode_Log(APP_ERROR_LOG);
$log->setIdent("UCI");

$dbo = null;
try {
	$dbo = new Ode_DBO(APP_DB_HOST_SPEC, APP_DB_NAME, APP_DB_USER, APP_DB_PASSWD);
} catch(Exception $e) {
	error_log($e->getMessage(), 0);;
}

$manager = new Ode_Manager();
$manager->setURI(REL_URL);

@session_start();

$auth = new Auth();

/**
 * Handle session timeout, if older than 10 minutes.
 */
$auth->timeout(Ode_Manager::getInstance()->action("auth", "logout"));

/**
 * Force login if user is not authorized
 * @todo Find another way to exclude pages that don't require authorization
 */
$nonAuthPages = array("auth", "uploader", "new_images", "api", "test");
if($auth->isAuth() == false && 
	!in_array(Ode_Manager::getInstance()->getPage(), $nonAuthPages)
	&& $_SERVER['SERVER_ADDR'] == APP_DEV_SERVER_ADDR) {
	
	//$webAuth->logout();
	//if($webAuthUser == false) { // only force login if not using webauth
		header("Location: " . Ode_Manager::getInstance()->action("auth"));
		exit();
	//}
}

/**
 * For asset requests
 */
if(Ode_Auth::getInstance()->hasSession()) {
    $order = new Order(Ode_Auth::getInstance()->getSession()->id);
}

require_once 'ThumbBuilder.php';
$thumber = new ThumbBuidler($manager->getURI()."imggen.php");

require_once 'SearchManager.php';
$searching = new SearchManager();

require_once 'AssetManager.php';
$asset_manager = new AssetManager();

$controller = new Ode_Controller();
$controller->setPath(APP_PATH . "controllers");
$controller->setFileCreation(false);

/**
 * establish template
 */
$view = new Ode_View(APP_VIEW_PATH);
/**
 * Should the app create missing template files on the fly?
 */
$view->setFileCreation(false);

$view->assign("thumber", $thumber);
$view->assign("searching", $searching);
$view->assign("assetmanager", $asset_manager);

$view->setContentTemplate();

/**
 * Pump out the jams (output the template)
 */
$view->display($view->getLayout());
?>