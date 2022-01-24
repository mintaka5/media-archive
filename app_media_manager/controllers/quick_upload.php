<?php
$_SESSION[SESSION_UPLOAD_NAME] = array();

switch (Ode_Manager::getInstance()->getMode()) {
	default:
		switch(Ode_Manager::getInstance()->getTask()) {
			default:
                                /**
                                 * Clear out temp batch session items because we don't want 
                                 * to add pre-selected ones from
                                 * the asset list to the uplad batch
                                 */
                                
                                AssetManager::getInstance()->clear();
                            
				//Util::debug(Ode_Auth::getInstance()->getSession()->organizations());
				$orgs = Ode_Auth::getInstance()->getSession()->organizations();
				Ode_View::getInstance()->assign("orgs", $orgs);
				break;
		}
		break;
}
?>
