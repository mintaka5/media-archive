<?php
require_once './init.php';

switch(Ode_Manager::getInstance()->getMode()) {
	default:
            switch(Ode_Manager::getInstance()->getTask()) {
                default:
                    
                    break;
                case 'available':
                    $asset_id = $_GET['asset_id'];
                    
                    $groups = Tasks_Groups::availableToAsset(Ode_Auth::getInstance(), DBO_Asset::getOneById($asset_id), PDO::FETCH_CLASS);
                    
                    Ode_View::getInstance()->assign('groups', $groups);
                    
                    echo Ode_View::getInstance()->fetch('ajax/assets/available_groups.tpl.php');
                    break;
                case 'search':
                    $asset_id = $_POST['asset_id'];
                    
                    $groups = Tasks_Groups::searchAvailable(Ode_Auth::getInstance(), $_POST['qry'], DBO_Asset::getOneById($asset_id), PDO::FETCH_CLASS);
                    
                    Ode_View::getInstance()->assign('groups', $groups);
                    
                    echo Ode_View::getInstance()->fetch('ajax/assets/available_groups.tpl.php');
                    break;
            }
            break;
	case 'rights':
		switch(Ode_Manager::getInstance()->getTask()) {
			default: break;
			case 'change':
				$group_id = $_POST['group_id'];
				$rights_id = $_POST['rights_id'];
				
				$group = DBO_Group::getOneById($group_id);
				$assets = $group->assets();
				
				foreach($assets as $asset) {
					DBO_Asset_Metadata::add(DBO_Asset_Metadata::META_RIGHTS, $rights_id, $asset->id, true);
				}
				
				Util::json($_POST);
				exit();
				break;
		}
		break;
	case 'meta':
		switch(Ode_Manager::getInstance()->getTask()) {
			default: break;
			case 'update':
				$group_id = $_POST['group_id'];
				
				$group = DBO_Group::getOneById($group_id);
				$assets = $group->assets();
				
				foreach($assets as $asset) {
					try {
						$metadata = new Metadata_XMP(IMAGE_STORAGE_PATH.$asset->filename);
						$written = $metadata->write($asset);
					} catch (Exception $e) {
						error_log($e->getMessage(), 1, APP_ADMIN_EMAIL);
					}
				}
				
				Util::json($_POST);
				exit();
				break;
		}
		break;
        case 'has':
            switch(Ode_Manager::getInstance()->getTask()) {
                default:break;
                case 'default':
                    $bool = false;
                    
                    $group = DBO_Group::getOneById($_POST['group']);
                    if($group->defaultAsset() != false) {
                        $bool = true;
                    }
                    
                    Util::json($bool);
                    exit();
                    break;
            }
            break;
	case 'keywords':
		switch(Ode_Manager::getInstance()->getTask()) {
			default:break;
			case 'add':
				$assets = DBO_Asset::getAllByGroup($_POST['gid']);
				
				foreach($assets as $asset) {
					DBO_Keyword_Asset_Cnx::assign($asset->id, $_POST['kword']);
				}
				break;
			case 'rmv':
				$assets = DBO_Asset::getAllByGroup($_POST['gid']);
				
				foreach($assets as $asset) {
					DBO_Keyword_Asset_Cnx::unassign($asset->id, $_POST['kword']);
				}
				break;
			case 'new':
				$assets = DBO_Asset::getAllByGroup($_POST['gid']);
				
				$newId = DBO_Keyword::add($_POST['kword']);
				
				foreach($assets as $asset) {
					DBO_Keyword_Asset_Cnx::assignById($asset->id, $newId);
				}
				
				Util::json(array("formdata" => $_POST, "keyword_id" => $newId));
				exit();
				break;
		}
		break;
	/*case 'loc':
		switch(Ode_Manager::getInstance()->getTask()) {
			case 'all':
				$assets = Ode_DBO::getInstance()->query("
					SELECT asset.*
					FROM asset_group_cnx AS cnx
					LEFT JOIN assets AS asset ON (asset.id = cnx.asset_id)
					WHERE cnx.group_id = " . Ode_DBO::getInstance()->quote($_POST['group_id'], PDO::PARAM_STR) . "
					AND asset.is_deleted = 0
				")->fetchAll(PDO::FETCH_ASSOC);
				
				Util::json($assets);
				exit();
				break;
			case 'remove':
				$assets = DBO_Asset::getAllByGroup($_POST['group_id']);
				//Util::debug($assets); die();
				
				Ode_DBO::getInstance()->beginTransaction();
				
				foreach($assets as $asset) {
					$sth = Ode_DBO::getInstance()->prepare("
						UPDATE " . DBO_Asset::TABLE_NAME . "
						SET
							location = NULL,
							lat = NULL,
							lng = NULL,
							modified = NOW()
						WHERE id = :id
					");
					$sth->bindParam(":id", $asset->id, PDO::PARAM_STR, 50);
					
					try {
						$sth->execute();
					} catch(PDOException $e) {
						Ode_Error::mail($e->getMessage(), __LINE__, __FILE__, APP_ADMIN_EMAIL);
					} catch(Exception $e) {
						Ode_Error::mail($e->getMessage(), __LINE__, __FILE__, APP_ADMIN_EMAIL);
					}
				}
				
				Ode_DBO::getInstance()->commit();
				
				Util::json($_POST);
				exit();
				break;
			case 'set':
				$assets = DBO_Asset::getAllByGroup($_POST['gid']);
	
				Ode_DBO::getInstance()->beginTransaction();
	
				foreach($assets as $asset) {
					$sth = Ode_DBO::getInstance()->prepare("
							UPDATE " . DBO_Asset::TABLE_NAME . "
							SET
								location = :loc,
								modified_by = :user,
								modified = NOW()
							WHERE id = :id
						");
					$sth->bindValue(":loc", trim($_POST['loc']), PDO::PARAM_STR);
					$sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
					$sth->bindValue(":id", $asset->id, PDO::PARAM_STR);
						
					try {
						$sth->execute();
					} catch(PDOException $e) {
						Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
					}
				}
	
				Ode_DBO::getInstance()->commit();
	
				Util::json($_POST);
				exit();
				break;
			case 'edit':
				$assets = DBO_Asset::getAllByGroup($_POST['group_id']);
				//Util::debug($assets);
				
				Ode_DBO::getInstance()->beginTransaction();
				
				foreach($assets as $asset) {
					$sth = Ode_DBO::getInstance()->prepare("
						UPDATE " . DBO_Asset::TABLE_NAME . "
						SET
							location = :loc,
							lat = :lat,
							lng = :lon,
							modified = NOW()
						WHERE id = :id
					");
					$sth->bindParam(":loc", $_POST['loc'], PDO::PARAM_STR, 255);
					$sth->bindParam(":lat", $_POST['lat'], PDO::PARAM_STR, 25);
					$sth->bindParam(":lon", $_POST['lon'], PDO::PARAM_STR, 25);
					$sth->bindParam(":id", $asset->id, PDO::PARAM_STR, 50);
					
					
					try {
						$sth->execute();
					} catch(PDOException $e) {
						Ode_Error::mail($e->getMessage(), __LINE__, __FILE__, APP_ADMIN_EMAIL);
					} catch(Exception $e) {
						Ode_Error::mail($e->getMessage(), __LINE__, __FILE__, APP_ADMIN_EMAIL);
					}
				}
				
				Ode_DBO::getInstance()->commit();
				
				Util::json($_POST);
				exit();
				break;
		}
		break;*/
	case 'del':
		switch(Ode_Manager::getInstance()->getTask()) {
			default:
				$sth = Ode_DBO::getInstance()->prepare("
					UPDATE groups
					SET
						is_deleted = 1,
						modified = NOW(),
						modified_by = :modified_by
					WHERE id = :id
				");
				$sth->bindValue(":modified_by", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
				$sth->bindValue(":id", $_POST['_id'], PDO::PARAM_STR);
				
				try {
					$sth->execute();
					
					/**
				 	 * @todo Figure out a better way than simply deleting the association, 
				 	 * to remove the group's associated assets
				 	 */
					$sth = Ode_DBO::getInstance()->prepare("
						DELETE FROM asset_group_cnx
						WHERE group_id = :group_id
					");
					$sth->bindValue(":group_id", $_POST['_id'], PDO::PARAM_STR);
					
					try {
						$sth->execute();
					} catch (PDOException $e) {
						//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
					
						Util::json(false);
					}
					
					Util::json($_POST);
				} catch (PDOException $e) {
					//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                    error_log($e->getMessage(), 0);
					
					Util::json(false);
				}
				break;
		}
		break;
	case 'edit':
		$sth = Ode_DBO::getInstance()->prepare("
			UPDATE groups
			SET
				title = :title,
				modified = NOW(),
				modified_by = :modified_by
			WHERE id = :id
		");
		$sth->bindValue(":title", trim($_POST['value']), PDO::PARAM_STR);
		$sth->bindValue(":modified_by", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
		$sth->bindValue(":id", $_POST['_id'], PDO::PARAM_STR);
		
		try {
			$sth->execute();
			
			echo trim($_POST['value']);
		} catch(PDOException $e) {
			//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
            error_log($e->getMessage(), 0);
			
			Util::json(false);
		}
		exit();
		break;
	case 'add':
		switch(Ode_Manager::getInstance()->getTask()) {
			default:
				
				break;
			case 'new':
				$newId = UUID::get();
				
				$sth = Ode_DBO::getInstance()->prepare("
					INSERT INTO groups (id, title, created, modified, modified_by)
					VALUES (:id, :title, NOW(), NOW(), :modified_by)
				");
				$sth->bindValue(":id", $newId, PDO::PARAM_STR);
				$sth->bindValue(":title", trim($_POST['title']), PDO::PARAM_STR);
				$sth->bindValue(":modified_by", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
				
				try {
					$sth->execute();
					
					$sth = Ode_DBO::getInstance()->prepare("
						INSERT INTO group_container_cnx (group_id, container_id)
						VALUES (:gid, :cid)
					");
					$sth->bindValue(":gid", $newId, PDO::PARAM_STR);
					$sth->bindValue(":cid", $_POST['_cid'], PDO::PARAM_STR);
					
					try {
						$sth->execute();
					} catch(PDOException $e) {
						//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
						
						Util::json(false);
					}
					
					Util::json($_POST);
				} catch(PDOException $e) {
					//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                    error_log($e->getMessage(), 0);
					
					Util::json(false);
				}
				break;
			case 'cur':
				$sth = Ode_DBO::getInstance()->prepare("
					INSERT INTO group_container_cnx (group_id, container_id)
					VALUES (:gid, :cid)
				");
				$sth->bindValue(":cid", $_POST['_cid'], PDO::PARAM_STR);
				$sth->bindValue(":gid", $_POST['_gid'], PDO::PARAM_STR);
				
				try {
					$sth->execute();	
				
					Util::json($_POST);
				} catch(PDOException $e) {
					//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                    error_log($e->getMessage(), 0);
					
					Util::json(false);
				}
				break;
			case 'quick':
				$newId = UUID::get();
				
				$sth = Ode_DBO::getInstance()->prepare("
					INSERT INTO " . DBO_Group::TABLE_NAME . " (id, title, created, modified, modified_by)
					VALUES (:id, :title, NOW(), NOW(), :user)
				");
				$sth->bindValue(":id", $newId, PDO::PARAM_STR);
				$sth->bindValue(":title", $_POST['title'], PDO::PARAM_STR);
				$sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
				
				try {
					$sth->execute();
				} catch(PDOException $e) {
					//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                    error_log($e->getMessage(), 0);
				
					Util::json(false);
				}
				
				Util::json(array("formdata" => $_POST, "gid" => $newId));
				exit();
				break;
		}
		break;
	case 'appr':
		$grp = DBO_Group::getOneById($_POST['group']);
		
		switch(Ode_Manager::getInstance()->getTask()) {
			default:
			case 'yes':
				$approval = DBO_Group::approve($grp->id);
			
				Util::json(array("formdata" => $_POST, "approval" => $approval));
				exit();
				break;
			case 'no':				
				$approval = DBO_Group::approve($grp->id, false);
				
				/**
				 * Reset lineitem approval, since the group's publicity has been revoked
				 */
				$assets = $grp->assets();
				foreach($assets as $asset) {
					DBO_Order_LineItem::approveByAsset($asset->id, 0);
				}
					
				Util::json(array("formdata" => $_POST, "approval" => $approval));
				exit();
				break;
		}
		break;
	case 'asset':
		switch (Ode_Manager::getInstance()->getTask()) {
			default:
				
				break;
			case 'default':
				try {
					$results = DBO_Asset_Group_Cnx::setDefaultAsset($_POST['_aid'], $_POST['_gid']);
					
					Util::json($results);
				} catch(Exception $e) {
					//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                    error_log($e->getMessage(), 0);
					
					Util::json(false);
				}
				break;
			case 'add':
                                $asset_id = $_POST['aid'];
                                $title = $_POST['title'];
                                
                                $group_id = Tasks_Groups::add($title, Ode_Auth::getInstance()->getSession()->id, false);
                            
                                if($group_id != false) {
                                    Tasks_Assets::assignGroup($asset_id, $group_id);
                                    
                                    Util::json(array('group_id' => $group_id, 'asset_id' => $asset_id));
                                } else {
                                    Util::json(false);
                                }
				break;
			case 'assign':
				$sth = Ode_DBO::getInstance()->prepare("
					INSERT INTO " . DBO_Asset_Group_Cnx::TABLE_NAME . " (asset_id, group_id)
					VALUES (:asset_id, :group_id)
				");
				$sth->bindValue(":asset_id", $_POST['_aid'], PDO::PARAM_STR);
				$sth->bindValue(":group_id", $_POST['_gid'], PDO::PARAM_STR);
				
				try {
					$sth->execute();
					
					$cnxId = Ode_DBO::getInstance()->query("
						SELECT a.id 
						FROM " .DBO_Asset_Group_Cnx::TABLE_NAME . " AS a
						WHERE a.asset_id = " . Ode_DBO::getInstance()->quote($_POST['_aid'], PDO::PARAM_STR) . "
						AND a.group_id = " . Ode_DBO::getInstance()->quote($_POST['_gid'], PDO::PARAM_STR) . "
						LIMIT 0,1
					")->fetchColumn();
					
					$sth = Ode_DBO::getInstance()->prepare("
						INSERT IGNORE INTO asset_group_def (cnx_id)
						VALUES (:cnx_id)
					");
					$sth->bindValue(":cnx_id", $cnxId, PDO::PARAM_INT);
					
					try {
						$sth->execute();
					} catch (PDOException $e) {
						//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
						
						Util::json(false);
					}	
					
					Util::json($_POST);
				} catch(PDOException $e) {
					//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                    error_log($e->getMessage(), 0);
					
					Util::json(false);
				}
				break;
			case 'del':
				$json = new Services_JSON();
				
				DBO_Asset_Group_Cnx::removeAssetFromGroup($_POST['aid'], $_POST['gid']);
				
				Util::json(array("asset_id" => $_POST['aid'], "group_id" => $_POST['gid']));
				break;
			case 'activity':
				switch($_POST['public']) {
					default:
					case 'yes':
						$activity = DBO_Asset::approveInGroup($_POST['group']);
					
						Util::json(array("formdata" => $_POST, "activity" => $activity));
						exit();
						break;
					case 'no':
						$activity = DBO_Asset::approveInGroup($_POST['group'], false);
							
						Util::json(array("formdata" => $_POST, "activity" => $activity));
						exit();
						break;
				}
				break;
		}
		break;
	case 'outtake':
		$assets = DBO_Asset::getAllByGroup($_POST['gid']);
		
		switch(Ode_Manager::getInstance()->getTask()) {
			default:break;
			case 'yes':
				foreach($assets as $asset) {
					DBO_Asset_Outtake::set($asset->id);
				}
				break;
			case 'no':
				foreach($assets as $asset) {
					DBO_Asset_Outtake::un_set($asset->id);
				}
				break;
		}
		break;
	case 'select':
		$assets = DBO_Asset::getAllByGroup($_POST['gid']);
		
		switch(Ode_Manager::getInstance()->getTask()) {
			default:break;
			case 'yes':
				foreach($assets as $asset) {
					DBO_Asset_Select::set($asset->id);
				}
				break;
			case 'no':
				foreach($assets as $asset) {
					DBO_Asset_Select::un_set($asset->id);
				}
				break;
		}
		break;
	case 'pubd':
		$assets = DBO_Asset::getAllByGroup($_POST['gid']);
		
		switch(Ode_Manager::getInstance()->getTask()) {
			default:break;
			case 'yes':
				foreach($assets as $asset) {
					DBO_Asset_Published::set($asset->id, $_POST['pub_name'], $_POST['date']);
				}
				break;
			case 'no':
				foreach($assets as $asset) {
					DBO_Asset_Published::un_set($asset->id);
				}
				break;
		}
		break;
	case 'featured':
		$group = DBO_Group::getOneById($_POST['group']);
		
		$grpIds = Ode_DBO::getInstance()->query("
			SELECT a.value
			FROM properties AS a
			WHERE a.is_enabled = 1
			AND a.machine_name = 'featured_groups'
		")->fetchColumn();
		$json = new Services_JSON();
		$grpIds = $json->decode($grpIds);
		
		switch (Ode_Manager::getInstance()->getTask()) {
			default: break;
			case 'yes':
				if($group->is_approved == 1) {
					// set featured
					$grpIds[] = $group->id;
					
					$grpIdsStr = $json->_encode($grpIds);
					
					$sth = Ode_DBO::getInstance()->prepare("
						UPDATE properties
						SET
							value = :val
						WHERE machine_name = 'featured_groups'
					");
					$sth->bindValue(":val", $grpIdsStr, PDO::PARAM_STR);
					
					try {
						$sth->execute();
					} catch (Exception $e) {
						//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
					}
				}
				break;
			case 'no':
				$newGrpIds = array();
				foreach($grpIds as $grpId) {
					if($group->id != $grpId) {
						$newGrpIds[] = $grpId;
					}
				}
				
				$newGrpIdsStr = $json->_encode($newGrpIds);
				
				$sth = Ode_DBO::getInstance()->prepare("
					UPDATE properties
					SET
						value = :val
					WHERE machine_name = 'featured_groups'
				");
				$sth->bindValue(":val", $newGrpIdsStr, PDO::PARAM_STR);
					
				try {
					$sth->execute();
				} catch (Exception $e) {
					//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                    error_log($e->getMessage(), 0);
				}
				break;
		}
		
		Util::json(array("postdata" => $_POST, "ispublic" => $group->is_approved));
		exit();
		break;
	case 'orgs':
		switch(Ode_Manager::getInstance()->getTask()) {
			default:
				
				break;
			case 'add':
				$org_id = $_POST['org_id'];
				$group_id = $_POST['group_id'];
                                
                                $group = DBO_Group::getOneById($group_id);
				
				DBO_Group_Metadata::add(DBO_Group_Metadata::META_ORG_ID_NAME, $org_id, $group->id);
                                
                                //DBO_Asset_Metadata::bulkAdd(DBO_Asset_Metadata::META_ORG_ID_NAME, $org_id, $group->assetIds(), true);
				
				Util::json($_POST);
				exit();
				break;
			case 'get':
				$group = DBO_Group::getOneById($_GET['group_id']);
				
				Ode_View::getInstance()->assign("group", $group);
				
				echo Ode_View::getInstance()->fetch("ajax/groups/orgs_get.tpl.php");
				exit();
				break;
			case 'del':
				$group_id = $_POST['group_id'];
				$org_id = $_POST['org_id'];
				
                                $group = DBO_Group::getOneById($group_id);
                                
				DBO_Group_Metadata::delete(DBO_Group_Metadata::META_ORG_ID_NAME, $org_id, $group->id);
                                
                                DBO_Asset_Metadata::bulkRemoveByValue(DBO_Asset_Metadata::META_ORG_ID_NAME, $group->assetIds(), $org_id);
				
				Util::json($_POST);
				exit();
				break;
			case 'find':
				$group_id = $_POST['group_id'];
				$terms = preg_replace("/[\r\s\t\n\W]+/", "%", $_POST['terms']);
				$terms = "%" . $terms . "%";
				
				$orgs = Ode_DBO::getInstance()->query("
					SELECT " . DBO_Organization::COLUMNS . "
					FROM " . DBO_Organization::TABLE_NAME . " AS a
					WHERE a.title LIKE " . Ode_DBO::getInstance()->quote($terms, PDO::PARAM_STR) . "
					AND a.is_deleted = 0
					AND a.id NOT IN (
						SELECT meta_value
						FROM " . DBO_Group_Metadata::TABLE_NAME . "
						WHERE meta_name = " . Ode_DBO::getInstance()->quote(DBO_Group_Metadata::META_ORG_ID_NAME, PDO::PARAM_STR) . "
						AND group_id = " . Ode_DBO::getInstance()->quote($group_id, PDO::PARAM_STR) . "
					)
				")->fetchAll(PDO::FETCH_CLASS, DBO_Organization::MODEL_NAME);
				
				Ode_View::getInstance()->assign("orgs", $orgs);
				Ode_View::getInstance()->assign("group_id", $group_id);
				
				echo Ode_View::getInstance()->fetch("ajax/groups/orgs_find.tpl.php");
				exit();
				break;
			case 'list':
				$group_id = $_GET['group_id'];
				
				$group = DBO_Group::getOneById($group_id);
                                
				Util::json($group->organizations("None assigned."));
				exit();
				break;
                        case 'assets':
                            $org_id = $_POST['org_id'];
                            $group_id = $_POST['group_id'];
                            
                            $group = DBO_Group::getOneById($group_id);
                            
                            DBO_Asset_Metadata::bulkAdd(DBO_Asset_Metadata::META_ORG_ID_NAME, $org_id, $group->assetIds());
                            
                            Util::json($_POST);
                            exit();
                            break;
		}
		break;
}
?>