<?php
require_once 'config.php';

@session_start();

set_include_path('.' . PATH_SEPARATOR . APP_INC_PATH . PATH_SEPARATOR . APP_PEAR_PATH . PATH_SEPARATOR . APP_ZEND_PATH);

require_once 'Autoloader.php';

$log = new Ode_Log(APP_ERROR_LOG);
$log->setIdent("UCI");

$db = new Ode_DBO(APP_DB_HOST_SPEC, APP_DB_NAME, APP_DB_USER, APP_DB_PASSWD);
?>