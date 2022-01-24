<?php
require_once './init.php';

switch(Ode_Manager::getInstance()->getMode()) {
	default: 
		switch(Ode_Manager::getInstance()->getTask()) {
			default:
				$catId = trim($_POST['cid']);
				
				$faqs = Ode_DBO::getInstance()->query("
					SELECT b.* 
					FROM ".FAQ_DATABASE_NAME.".faqcategoryrelations AS a
					LEFT JOIN ".FAQ_DATABASE_NAME.".faqcategories AS c ON (c.id = a.category_id)
					LEFT JOIN ".FAQ_DATABASE_NAME.".faqdata AS b ON (b.id = a.record_id)
					WHERE b.active = 'yes'
					AND (
						c.parent_id = " . Ode_DBO::getInstance()->quote($catId, PDO::PARAM_INT) . "
						OR c.id = " . Ode_DBO::getInstance()->quote($catId, PDO::PARAM_INT) . "
					)
				")->fetchAll(PDO::FETCH_OBJ);
			
				Ode_View::getInstance()->assign("faqs", $faqs);
				
				header("Content-Type:text/html");
				echo Ode_View::getInstance()->fetch("ajax/help/faqs_list.tpl.php");
				break;
		}
		break;
}

exit();
?>