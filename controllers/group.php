<?php
if(!Ode_Auth::getInstance()->isPrivate()) {
	exit("You do not have sufficient privileges to enter this page.");
}

switch(Ode_Manager::getInstance()->getMode()) {
	default:
		$groups = Ode_DBO::getInstance()->query("
			SELECT `group`.*
			FROM groups AS `group`
			WHERE `group`.is_deleted = 0
			ORDER BY `group`.title
			ASC
		")->fetchAll(PDO::FETCH_CLASS, "DBO_Group_Model");
		
		$pager = Pager::factory(array(
			"mode" => "Sliding",
			"append" => false,
			"urlVar" => "pagenum",
			"path" => Ode_Manager::getInstance()->action("group"),
			"fileName" => "%d",
			"itemData" => $groups,
			"perPage" => 10
		));
		Ode_View::getInstance()->assign("groups", $pager->getPageData());
		Ode_View::getInstance()->assign("links", $pager->getLinks());
		
		break;
	case 'download':
		switch(Ode_Manager::getInstance()->getTask()) {
			default:
				$group_id = trim($_GET['id']);
				
				$group = DBO_Group::getOneById($group_id);
				
				if($group != false) {
					$zipFilename = APP_UPLOAD_TMP_PATH . DIRECTORY_SEPARATOR . preg_replace("/[\s\W\n\r\t]+/", "_", $group->title) . ".zip";
					
					$zipper = new Archive_Zip($zipFilename);
					$items = $group->assets();
					
					$zipItems = array();
					foreach($items as $item) {
						$zipItems[] = IMAGE_STORAGE_PATH . $item->filename;
					}
					
					if($zipper->create($zipItems, array('remove_path' => IMAGE_STORAGE_PATH, 'remove_all_path' => true))) {
						$download = new HTTP_Download();
						$download->setFile($zipFilename);
						$download->setBufferSize(1000 * 1024);
						$download->setThrottleDelay(1); // 1 second
						
						$download->send();
						
						//unlink($zipFilename);
					} else {
						//echo "Zip archive creation has failed: " . $zipper->errorInfo(true);
						error_log("Zip archive creation has failed: " . $zipper->errorInfo(true), 0);
						
					}
				}
				break;
		}
		break;
	case 'edit':
		switch(Ode_Manager::getInstance()->getTask()) {
			default:
				$group = Ode_DBO::getInstance()->query("
					SELECT `group`.*
					FROM groups AS `group`
					WHERE `group`.id = " . Ode_DBO::getInstance()->quote($_GET['id'], PDO::PARAM_STR) . "
					LIMIT 0,1
				")->fetchObject("DBO_Group_Model");
				
				$form = new HTML_QuickForm2("editGroupForm");
				$form->addDataSource(new HTML_QuickForm2_DataSource_Array(array(
					"title" => $group->title,
					"is_approved" => $group->is_approved
				)));
				$form->setAttribute("action", Ode_Manager::getInstance()->action("group", "edit", null, array("id", $group->id)));
				
				$titleTxt = $form->addText("title")->setLabel("Title");
				
				$approvedChk = $form->addCheckbox("is_approved")->setContent("Enabled");
				
				$submitBtn = $form->addSubmit("submitBtn")->setAttribute("value", "Update");
				
				if($form->validate()) {
					$sth = Ode_DBO::getInstance()->prepare("
						UPDATE groups
						SET
							title = :title,
							is_approved = :is_approved
						WHERE id = :id
					");
					$sth->bindValue(":title", trim($_POST[$titleTxt->getName()]), PDO::PARAM_STR);
					$sth->bindValue(":is_approved", (isset($_POST[$approvedChk->getName()])) ? 1 : 0, PDO::PARAM_INT);
					$sth->bindValue(":id", $group->id, PDO::PARAM_STR);
					
					try {
						$sth->execute();
						
						header("Location: " . Ode_Manager::getInstance()->action("group"));
						exit();
					} catch(PDOException $e) {
						//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
					}
				}
				
				Ode_View::getInstance()->assign("form", $form->render(Ode_View::getInstance()->getFormRenderer()));
				
				break;
			case 'new':
				$sth = Ode_DBO::getInstance()->prepare("CALL addNewGroup(:title, :container_id, :modified_by, @id)");
				$sth->bindValue(":title", trim($_POST['grpTitle']), PDO::PARAM_STR);
				$sth->bindValue(":container_id", empty($_POST['grpContainerId']) ? null : trim($_POST['grpContainerId']), empty($_POST['grpContainerId']) ? PDO::PARAM_NULL : PDO::PARAM_STR);
				$sth->bindValue(":modified_by", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
				
				try {
					$sth->execute();
					
					$newId = Ode_DBO::getInstance()->query("SELECT @id")->fetchColumn();
					header("Location: " . Ode_Manager::getInstance()->action("group", "edit", null, array("id", $newId)));
					exit();
				} catch(PDOException $e) {
					//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                    error_log($e->getMessage(), 0);
				}
				break;
		}
		break;
	case 'view':
		/**
		 * 
		 * Unset image upload IDs
		 * @var array
		 */
		unset($_SESSION[SESSION_UPLOAD_NAME]);
		
		$group = Ode_DBO::getInstance()->query("
			SELECT grp.*
			FROM groups AS grp
			WHERE grp.id = " . Ode_DBO::getInstance()->quote($_GET['id'], PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchObject("DBO_Group_Model");
		
		$rights = Ode_DBO::getInstance()->query("
			SELECT " . DBO_Properties::COLUMNS . "
			FROM " . DBO_Properties::TABLE_NAME . " AS a
			WHERE a.machine_name = " . Ode_DBO::getInstance()->quote(DBO_Properties::RIGHTS_PROPERTY_NAME, PDO::PARAM_STR) . "
			AND a.is_enabled = 1
			ORDER BY a.value ASC 
		")->fetchAll(PDO::FETCH_OBJ);

		Ode_View::getInstance()->assign("rights", $rights);
		Ode_View::getInstance()->assign("group", $group);
		break;
	case 'del':
		$sth = Ode_DBO::getInstance()->prepare("
			UPDATE groups
			SET 
				is_deleted = 1,
				modified = NOW()
				modified_by = :modified_by
			WHERE id = :id
		");
		$sth->bindValue(":modified_by", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
		$sth->bindValue(":id", trim($_GET['id']), PDO::PARAM_STR);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
            error_log($e->getMessage(), 0);
		}
		break;
}
?>