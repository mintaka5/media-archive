<?php

/**
 * Allow only administrators
 */
if (!Ode_Auth::getInstance()->isAdmin() && !Ode_Auth::getInstance()->isManager()) {
    header("Location: " . Ode_Manager::getInstance()->action("index"));
    exit();
}

switch (Ode_Manager::getInstance()->getMode()) {
    default:
        switch (Ode_Manager::getInstance()->getTask()) {
            default:

                break;
        }
        break;
   case 'edit':
       switch(Ode_Manager::getInstance()->getTask()) {
            default:
                $org = DBO_Organization::getOneById($_GET['id']);
                
                Util::debug($org->metadata(DBO_Organization_Metadata::META_FLICKR_ACCESS_TOKEN, true));
                
                Ode_View::getInstance()->assign('org', $org);
                break;
       }
       break;
}
?>