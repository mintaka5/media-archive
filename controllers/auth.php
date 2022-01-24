<?php
switch(Ode_Manager::getInstance()->getMode()) {
	default:
		$form = new HTML_QuickForm2("loginForm");
		$form->setAttribute("action", Ode_Manager::getInstance()->action("auth"));
		
		$userTxt = $form->addText("uname")->setLabel("Username");
		$userTxt->setAttribute("class", "textField");
		$userTxt->addRule("required", "Required");
		
		$passTxt = $form->addPassword("pass")->setLabel("Password");
		$passTxt->setAttribute("class", "textField");
		$passTxt->addRule("required", "Required");
		
		$submitBtn = $form->addSubmit("submit")->setAttribute("value", "Login");
		$submitBtn->setAttribute("class", "goBtn");
		
		if($form->validate()) {
			$user = Ode_DBO::getInstance()->query("
				SELECT user.*
				FROM users AS user
				WHERE user.username = " . Ode_DBO::getInstance()->quote(trim($_POST['uname']), PDO::PARAM_STR) . "
				AND user.password = MD5(" . Ode_DBO::getInstance()->quote(trim($_POST['pass']), PDO::PARAM_STR) . ")
				AND user.is_active = 1
				AND user.is_deleted = 0
				LIMIT 0,1
			")->fetchObject("DBO_User_Model");
			
			if($user != false) {
				Ode_Auth::getInstance()->setSession($user);
				
				header("Location: " . Ode_Manager::getInstance()->getURI());
				exit();
			} else {
				Ode_View::getInstance()->assign("login_error", 1);
			}
		}
		
		Ode_View::getInstance()->assign("form", $form->render(Ode_View::getInstance()->getFormRenderer()));
		break;
	case 'logout':
		AssetManager::getInstance()->clear();
		
		Order::remove();
		
		Ode_Auth::getInstance()->killSession();
		
		/*header("Location: " . Ode_Manager::getInstance()->action("index"));
		exit();*/
		break;
}
?>