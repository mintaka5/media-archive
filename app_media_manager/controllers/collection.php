<?php
switch(Ode_Manager::getInstance()->getMode()) {
	default:
	case 'view':
		$container = DBO_Container::getOneById($_GET['id']);
		
		Ode_View::getInstance()->assign("container", $container);
		break;
}
?>