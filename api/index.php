<?php
require_once './init.php';

require_once 'restler/restler.php';
require_once 'restler/xmlformat.php';
require_once 'API/Auth.php';
require_once 'API/Assets.php';

$rest = new Restler();

$rest->addAuthenticationClass("API_Auth");
$rest->setSupportedFormats('JsonFormat', 'XmlFormat');
$rest->addAPIClass("Assets");
$rest->handle();
?>