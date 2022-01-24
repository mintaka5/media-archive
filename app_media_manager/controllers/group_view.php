<?php
/**
 * 
 * Sets the group we are viewing so we know what group the asset we are viewing is in
 * @var string
 */
$_SESSION[CURRENT_GROUP_VAR] = trim($_GET['id']);

switch(Ode_Manager::getInstance()->getMode()) {
	default:
        Ode_DBO::getInstance()->setAttribute(PDO::ATTR_PERSISTENT, true);
        
        $sql = "SELECT " . DBO_Asset::COLUMNS . " FROM view_publicGroupImages AS a WHERE a.group_id = " . Ode_DBO::getInstance()->quote($_SESSION[CURRENT_GROUP_VAR], PDO::PARAM_STR);
		
		$assets = Ode_DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_CLASS, DBO_Asset::MODEL_NAME);
		
		$pager = Pager::factory(array(
			'perPage' => 9,
			'urlVar' => "page",
			'mode' => "Sliding",
			'append' => true,
			'path' => Ode_Manager::getInstance()->action("group_view", null, null, array("id", trim($_GET['id']))),
			'delta' => 5,
			'itemData' => $assets
		));
		
		Ode_View::getInstance()->assign("group", DBO_Group::getOneById($_SESSION[CURRENT_GROUP_VAR]));
		Ode_View::getInstance()->assign("assets", $pager->getPageData());
		Ode_View::getInstance()->assign("pagelinks", $pager->getLinks());
                
        Ode_DBO::getInstance()->setAttribute(PDO::ATTR_PERSISTENT, false);
		break;
}
?>