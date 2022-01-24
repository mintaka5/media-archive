<?php
if(!Ode_Auth::getInstance()->isPrivate()) {
	exit("You do not have sufficient privileges to enter this page.");
}

$pageId = (isset($_GET['pageID'])) ? $_GET['pageID'] : 1;

switch(Ode_Manager::getInstance()->getMode()) {
	case false:
	default:
		switch(Ode_Manager::getInstance()->getTask()) {
			case false:
			default:
				/**
				 * only admins have access to groups globally
				 */
				if(Ode_Auth::getInstance()->isAdmin()) {
					$sql = "SELECT " . DBO_Group::COLUMNS . "
						FROM " . DBO_Group::TABLE_NAME . " AS a
						WHERE a.is_deleted = 0
						ORDER BY a.created
						DESC";
				} else {
					$org_ids = Util::dbQuoteListItems(DBO_User_Organization_Cnx::getUserOrgIDs(Ode_Auth::getInstance()->getSession()->id));
					$org_ids = (!empty($org_ids)) ? implode(',', $org_ids) : "''";
					
					$sql = "SELECT " . DBO_Group::COLUMNS . "
							FROM " . DBO_Group::TABLE_NAME . " AS a
							LEFT JOIN " . DBO_Group_Metadata::TABLE_NAME . " AS b ON (b.group_id = a.id)
							WHERE a.is_deleted = 0
							AND b.meta_name = " . Ode_DBO::getInstance()->quote(DBO_Group_Metadata::META_ORG_ID_NAME, PDO::PARAM_STR) . "
							AND b.meta_value IN (" . $org_ids . ")
							GROUP BY a.id";
				}
				
				$groups = Ode_DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_CLASS, DBO_Group::MODEL_NAME);
				
				$user_orgs = Ode_Auth::getInstance()->getSession()->organizations();
				
				$pager = Pager::factory(array(
					'perPage' => 10,
					'mode' => "Jumping",
					'delta' => 4,
					'itemData' => $groups
				));
				
				Ode_View::getInstance()->assign("groups", $pager->getPageData());
				Ode_View::getInstance()->assign("assetlinks", $pager->getLinks());
				Ode_View::getInstance()->assign("num_pages", $pager->numPages());
				Ode_View::getInstance()->assign("pageid", $pageId);
				Ode_View::getInstance()->assign("num_items", count($groups));
				Ode_View::getInstance()->assign("user_orgs", $user_orgs);
				break;
		}
		break;
	case 'search':
		switch(Ode_Manager::getInstance()->getTask()) {
			case false:
			default:
				$plainQry = trim($_GET['q']);
				
				$groups = DBO_Group::findByUserOrgs($plainQry, Ode_Auth::getInstance()->getSession()->id);
				
				$pager = Pager::factory(array(
					'perPage' => 10,
					'mode' => "Jumping",
					'delta' => 4,
					'itemData' => $groups
				));
				
				Ode_View::getInstance()->assign("groups", $pager->getPageData());
				Ode_View::getInstance()->assign("assetlinks", $pager->getLinks());
				Ode_View::getInstance()->assign("num_pages", $pager->numPages());
				Ode_View::getInstance()->assign("query", $plainQry);
				Ode_View::getInstance()->assign("pageid", $pageId);
				Ode_View::getInstance()->assign("num_items", count($groups));
				break;
		}
		break;
	case 'add':
		switch(Ode_Manager::getInstance()->getTask()) {
			case false:
			default:
                            $uuid = Tasks_Groups::add($_POST['groupTitle'], Ode_Auth::getInstance()->getSession()->id, (isset($_POST['userOrg'])) ? $_POST['userOrg'] : false);
                            
                            if($uuid != false) {
                                header("Location: " . Ode_Manager::getInstance()->action("group", "view", null, array("id", $uuid)));
                                exit();
                            }
                            break;
		}
		break;
	case 'featured':
		$group = DBO_Group::getOneById($_GET['id']);
	
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
	
		if(isset($_GET['r_mode'])) {
			header("Location: ". Ode_Manager::getInstance()->action("admin_groups", $_GET['r_mode'], null, array("pageID", $pageId), array("q", $_GET['q'])));
		} else {
			header("Location: " . Ode_Manager::getInstance()->action("admin_groups", null, null, array("pageID", $pageId)));
		}
		exit();
		break;
}