<?php
switch(Ode_Manager::getInstance()->getMode()) {
	default:
		$assets = Ode_DBO::getInstance()->query("
			SELECT " . DBO_Asset::COLUMNS . "
			FROM assets AS a
			ORDER BY a.created
			ASC
		")->fetchAll(PDO::FETCH_CLASS, "DBO_Asset_Model");
		Ode_View::getInstance()->assign("assets", $assets);
		break;
	case 'download':
		switch(Ode_Manager::getInstance()->getTask()) {
			default: break;
			case 'single':
                            $asset_id = $_GET['id'];

                            $asset = DBO_Asset::getOneById($asset_id);

                            try {
                                $download = new HTTP_Download();
                                $download->setFile(IMAGE_STORAGE_PATH . $asset->filename);
                                $download->setBufferSize(1000 * 1024);
                                $download->setThrottleDelay(1);

                                $download->send();
                            } catch(Exception $e) {
                                error_log($e->getMessage(), 0);
                                header("Location: " . Ode_Manager::getInstance()->friendlyAction("assets", "download", "failed"));
                                exit();
                            }

                            //exit();
                            break;
			case 'batch':
				$asset_ids = explode(",", $_GET['asset_ids']);
				
				if(!empty($asset_ids)) {
					$zipFilename = APP_UPLOAD_TMP_PATH . DIRECTORY_SEPARATOR . "batch_" . date("YmdHis") . ".zip";
					
					$zipper = new Archive_Zip($zipFilename);
					$zipItems = array();
					
					foreach($asset_ids as $aid) {
						$asset = DBO_Asset::getOneById($aid);
						
						$zipItems[] = IMAGE_STORAGE_PATH . $asset->filename;
					}
					
					if($zipper->create($zipItems, array('remove_path' => IMAGE_STORAGE_PATH, 'remove_all_path' => true))) {
                                            try {
						$download = new HTTP_Download();
						$download->setFile($zipFilename);
						$download->setBufferSize(1000 * 1024);
						$download->setThrottleDelay(1);
						
						$download->send();
                                            } catch(Exception $e) {
                                                error_log($e->getMessage());
                                                header("Location: " . Ode_Manager::getInstance()->friendlyAction("assets", "download", "failed"));
                                                exit();
                                            }
						
						//unlink($zipFilename);
					} else {
						error_log("Zip archive creation has failed: " . $zipper->errorInfo(true), 0);
					}
				}
				break;
                        case 'failed':
                           
                           break;
		}
		break;
	case 'del':
		DBO_Asset::delete(trim($_GET['id']));
		
		header("Location: " . Ode_Manager::getInstance()->action("index"));
		exit();
		break;
	case 'edit':
		$asset = Ode_DBO::getInstance()->query("
			SELECT " . DBO_Asset::COLUMNS . "
			FROM assets AS a
			WHERE a.id = " . Ode_DBO::getInstance()->quote(trim($_GET['id']), PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchObject("DBO_Asset_Model");
		
		$form = new HTML_QuickForm2("editAssetForm");
		$form->setAttribute("action", Ode_Manager::getInstance()->action("assets", "edit", null, array("id", $asset->id)));
		
		$form->addDataSource(new HTML_QuickForm2_DataSource_Array(array(
			"title" => $asset->title,
			"caption" => $asset->caption,
			"description" => $asset->description,
			"credit" => $asset->credit
		)));
		
		$titleTxt = $form->addText("title")->setLabel("Title");
		
		$captionTxt = $form->addText("caption")->setLabel("Caption");
		
		$descTxt = $form->addTextarea("description")->setLabel("Description");
		$descTxt->setAttribute("class", "descArea");
		
		$creditTxt = $form->addText("credit")->setLabel("Credit");
				
		$submitBtn = $form->addSubmit("submitBtn")->setAttribute("value", "Update");
		
		if($form->validate()) {
			$sth = Ode_DBO::getInstance()->prepare("
				UPDATE " . DBO_Asset::TABLE_NAME . "
				SET title = :title,
					caption = :caption,
					description = :desc,
					credit = :credit
				WHERE id = :id
			");
			$sth->bindValue(":title", trim($_POST[$titleTxt->getName()]), PDO::PARAM_STR);
			$sth->bindValue(":caption", trim($_POST[$captionTxt->getName()]), PDO::PARAM_STR);
			$sth->bindValue(":desc", trim($_POST[$descTxt->getname()]), PDO::PARAM_STR);
			$sth->bindValue(":credit", trim($_POST[$creditTxt->getName()]), PDO::PARAM_STR);
			$sth->bindValue(":id", $asset->id, PDO::PARAM_STR);
			
			try {
				$sth->execute();
				
				header("Location: " . Ode_Manager::getInstance()->action("assets", "view", null, array("id", $asset->id)));
				exit();
			} catch(PDOException $e) {
				//Ode_Log::getInstance()->log($e->getTraceAsString(), E_ERROR);
                error_log($e->getMessage(), 0);
			}
		}
		
		Ode_View::getInstance()->assign("asset", $asset);
		Ode_View::getInstance()->assign("form", $form->render(Ode_View::getInstance()->getFormRenderer()));
		break;
	case 'view':
		$asset = Ode_DBO::getInstance()->query("
			SELECT " . DBO_Asset::COLUMNS . "
			FROM " . DBO_Asset::TABLE_NAME . " AS a
			WHERE a.id = " . Ode_DBO::getInstance()->quote($_GET['id'], PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchObject("DBO_Asset_Model");
		
		$filename = IMAGE_STORAGE_PATH . $asset->filename;
		$dimensions = Util::imageDimensions($filename);
		
		
		$keywords = $asset->keywords();
		$keywordList = array();
		foreach($keywords as $kw) {
			$keywordList[] = $kw->keyword;
		}
		
		//$pelExif = new Metadata_PEL(IMAGE_STORAGE_PATH . $asset->filename);
		$exifData = new Metadata_XMP(IMAGE_STORAGE_PATH . $asset->filename);
		
		$rights = Ode_DBO::getInstance()->query("
			SELECT " . DBO_Properties::COLUMNS . "
			FROM " . DBO_Properties::TABLE_NAME . " AS a
			WHERE a.machine_name = " . Ode_DBO::getInstance()->quote(DBO_Properties::RIGHTS_PROPERTY_NAME, PDO::PARAM_STR) . "
			AND a.is_enabled = 1
			ORDER BY a.value ASC 
		")->fetchAll(PDO::FETCH_OBJ);
		
		$asset_rights = DBO_Asset_Metadata::get(DBO_Asset_Metadata::META_RIGHTS, $asset->id);
		
		Ode_View::getInstance()->assign("exif", $exifData);
		Ode_View::getInstance()->assign("keywordlist", implode(",", $keywordList));
		Ode_View::getInstance()->assign("asset", $asset);
		Ode_View::getInstance()->assign("asset_orgs", $asset->organizations()->getArrayCopy());
		Ode_View::getInstance()->assign("orgs", DBO_Organization::getAllActive());
		Ode_View::getInstance()->assign("rights", $rights);
		Ode_View::getInstance()->assign("asset_rights", $asset_rights);
		
		Ode_View::getInstance()->assign("filesize", Util::filesize($filename));
		Ode_View::getInstance()->assign("imgw", $dimensions->width->pixels);
		Ode_View::getInstance()->assign("imgh", $dimensions->height->pixels);
		Ode_View::getInstance()->assign("imgwin", $dimensions->width->inches);
		Ode_View::getInstance()->assign("imghin", $dimensions->height->inches);
		Ode_View::getInstance()->assign("resolution", $dimensions->resolution);
		break;
	case 'multi':
		switch(Ode_Manager::getInstance()->getTask()) {
			default:
				
				break;
			case 'del':
				$idList = Util::dbQuoteListItems(explode("|", trim($_GET['_ids'])));
				$idList = implode(", ", $idList);
				
				$sth = Ode_DBO::getInstance()->query("
					UPDATE assets
					SET
						is_deleted = 1,
						modified = NOW(),
						modified_by = " . Ode_DBO::getInstance()->quote(Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR) . "
					WHERE id IN (" . $idList . ")
				");
				
				try {
					$sth->execute();
					
					$assets = Ode_DBO::getInstance()->query("
						SELECT asset.*
						FROM assets AS asset
						WHERE asset.id  IN (" . $idList . ")
						ORDER BY asset.title
						ASC
					")->fetchAll(PDO::FETCH_CLASS, "DBO_Asset_Model");
					
					Ode_View::getInstance()->assign("assets", $assets);
				} catch(PDOException $e) {
					//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                    error_log($e->getMessage(), 0);
				}
				break;
			case 'grp':
				//Util::debug($_GET);
				break;
		}
		break;
	case 'feature':
		switch(Ode_Manager::getInstance()->getTask()) {
			default:
				$asset = DBO_Asset::getOneById($_GET['id']);
	
				/**
				 * Only if asset is public, then make it featured.
				 * We don't want admins mistakenly featuring non-public assets
				 */
				if($asset->is_active == 1) {
					$sth = Ode_DBO::getInstance()->prepare("
							UPDATE properties
							SET
							value = :id,
							modified = NOW()
							WHERE machine_name = 'featured_image'
							");
					$sth->bindValue(":id", $asset->id, PDO::PARAM_STR);
						
					try {
						$sth->execute();
					} catch (PDOException $e) {
						//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
					}
				}

				if(!isset($_GET['q'])) {
					header("Location: " . Ode_Manager::getInstance()->action("admin_assets", null, null, array("pageID", $_GET['pageID'])));
				} else {
					header("Location: " . Ode_Manager::getInstance()->action("admin_assets", null, null, array("pageID", $_GET['pageID']), array("_mode", $_GET['r_mode']), array("q", $_GET['q'])));
				}
				exit();
				break;
		}
		break;
}
?>