<?php
switch (Ode_Manager::getInstance()->getMode()) {
    default:
        switch (Ode_Manager::getInstance()->getTask()) {
            default:
                //Util::debug($_GET['ids']); die();
                $sql = "SELECT asset.*
		FROM assets AS asset
		WHERE asset.id IN (" . implode(",", $_GET['ids']) . ")";
                //Util::debug(implode(",", $_POST['ids'])); die();
                //echo $sql; die();

                $assets = Ode_DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_CLASS, "DBO_Asset_Model");

                Ode_View::getInstance()->assign("assets", $assets);

                break;
        }
        break;
}
?>