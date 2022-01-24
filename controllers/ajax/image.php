<?php
require_once './init.php';

switch(Ode_Manager::getInstance()->getMode()) {
	default:
		
		break;
	case 'edit':
		$asset = Ode_DBO::getInstance()->query("
			SELECT asset.*
			FROM assets AS asset
			WHERE asset.id = " . Ode_DBO::getInstance()->quote($_POST['id'], PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetch(PDO::FETCH_ASSOC);
		//Util::debug($asset);
		
		$form = new HTML_QuickForm2("editImageForm");
		$form->setAttribute("action", "javascript:return false;");
		
		$form->addDataSource(new HTML_QuickForm2_DataSource_Array($asset));
		
		$idHdn = $form->addHidden("id")->setValue($asset['id']);
		$modeHdn = $form->addHidden("_mode")->setValue("edit");
		
		$titleTxt = $form->addText("title")->setLabel("Title");
		$titleTxt->addRule("required", "Required");
		
		$captionTxt = $form->addText("caption")->setLabel("Caption");
		
		$descriptionTxt = $form->addTextarea("description")->setLabel("Description");
		$descriptionTxt->setAttribute("class", "descriptionText");
		
		$creditTxt = $form->addText("credit")->setLabel("Credit");
		
		$approvalChk = $form->addCheckbox("is_approved")->setContent("Approved");
		$approvalChk->setAttribute("id", "approvalChk");
		
		$submitBtn = $form->addButton("submitBtn")->setContent("Update");
		
		if($form->validate()) {
			//Util::debug($_POST);
			$sth = Ode_DBO::getInstance()->prepare("CALL editNewAsset(:id, :title, :caption, :description, :credit, :is_approved)");
			$sth->bindValue(":id", $_POST[$idHdn->getName()], PDO::PARAM_STR);
			$sth->bindValue(":title", $_POST[$titleTxt->getName()], PDO::PARAM_STR);
			$sth->bindValue(":caption", $_POST[$captionTxt->getName()], PDO::PARAM_STR);
			$sth->bindValue(":description", $_POST[$descriptionTxt->getName()], PDO::PARAM_STR);
			$sth->bindValue(":credit", $_POST[$creditTxt->getName()], PDO::PARAM_STR);
			$sth->bindValue(":is_approved", isset($_POST[$approvalChk->getName()]) ? 1 : 0, PDO::PARAM_INT);
			
			try {
				$sth->execute();
				
				Ode_View::getInstance()->assign("update", true);
			} catch (PDOException $e) {
				Ode_View::getInstance()->assign("update", false);
				
				//Ode_Log::getInstance()->log($e->getTraceAsString(), E_ERROR);
                error_log($e->getMessage(), 0);
			}
		}
		
		Ode_View::getInstance()->assign("asset", $asset);
		Ode_View::getInstance()->assign("form", $form->render(Ode_View::getInstance()->getFormRenderer()));
		
		header("Content-Type: text/html");
		echo Ode_View::getInstance()->fetch("ajax/image.tpl.php");
		break;
}

exit();
?>