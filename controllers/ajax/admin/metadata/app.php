<?php
require_once dirname(dirname(dirname(__FILE__))) . '/init.php';

switch(Ode_Manager::getInstance()->getMode()) {
    default:
        switch(Ode_Manager::getInstance()->getTask()) {
            default:
                $metadata = Ode_DBO::getInstance()->query("
                    SELECT a.id, a.slug, a.title, a.description, a.is_enabled
                    FROM metadata AS a
                    WHERE a.is_enabled = 1
                    ORDER BY a.title
                    ASC
                ")->fetchAll(PDO::FETCH_OBJ);

                echo json_encode($metadata);
                die();
                break;
        }
        break;
    case 'item':
        switch(Ode_Manager::getInstance()->getTask()) {
            case 'detail':
                $metadata = Ode_DBO::getInstance()->query("
                    SELECT a.id, a.slug, a.title, a.description, a.is_enabled
                    FROM metadata AS a
                    WHERE a.id = '" . $_GET['id'] . "'
                    LIMIT 0,1
                ")->fetchObject();

                echo json_encode($metadata);
                die();
                break;
        }
        break;
}