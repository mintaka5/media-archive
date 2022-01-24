<?php
/**
 * @todo check for isAuth or API key. There needs to be a way to shut this down 
 * from remote access
 */
require_once 'config.php';

require_once 'php_settings.php';

require_once 'Autoloader.php';

$dbo = new Ode_DBO(APP_DB_HOST_SPEC, APP_DB_NAME, APP_DB_USER, APP_DB_PASSWD);

@session_start();

$auth = new Auth();

/**
 * decrypt image settings as to not expose API key and username, etc...
 * only when not logged in
 */
if(isset($_GET['hid']) && !$auth->isAuth()) {
	$queryStr = Util::decrypt(APP_ENC_KEY, $_GET['hid']);
	parse_str($queryStr, $queryVars);
	$_GET = $queryVars;
}

require_once 'phpThumb_1.7.9/phpThumb.php';
//require_once 'phpthumb/uciThumb.php';

unset($_GET);
exit();
?>
