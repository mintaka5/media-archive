<?php
require_once './init.php';

switch (Ode_Manager::getInstance()->getMode()) {
    default:
        switch(Ode_Manager::getInstance()->getTask()) {
            default:
                
                break;
        }
        break;
    case 'api':
        switch (Ode_Manager::getInstance()->getTask()) {
            default: break;
            case 'gen':
                require_once 'phpass-0.3/PasswordHash.php';
                
                $hasher = new PasswordHash(8, false);
                $randomKey = Util::randomString();
                $userId = Ode_Auth::getInstance()->getSession()->id;
                $hash = $hasher->HashPassword($randomKey.$userId);
                
                DBO_User_API::create($userId, $randomKey, $hash);
                
                Util::json(array("formdata" => $_POST, "apikey" => $randomKey));
                exit();
                break;
        }
        break;
}
?>
