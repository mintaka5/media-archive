<?php
require_once './init.php';

switch(Ode_Manager::getInstance()->getMode()) {
	default: break;
	case 'remove':
		$container_id = $_POST['container_id'];
		
		$remove = DBO_Container::remove($container_id);
		
		Util::json($remove);
		exit();
		break;
}
?>