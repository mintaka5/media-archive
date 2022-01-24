<?php
require_once '../config.php';
require_once '../php_settings.php';

require_once 'Autoloader.php';

$dbo = new Ode_DBO(APP_DB_HOST_SPEC, APP_DB_NAME, APP_DB_USER, APP_DB_PASSWD);

@session_start();

$auth = new Auth();
?>