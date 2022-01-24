<?php
require_once './init.php';

switch(Ode_Manager::getInstance()->getMode()) {
	default:
		
		break;
	case 'batch':
		switch(Ode_Manager::getInstance()->getTask()) {
			default: break;
			case 'update':
				$photog_id = $_POST['photog_id'];
				$asset_ids = $_POST['asset_ids'];
				
				Ode_DBO::getInstance()->beginTransaction();
				
				foreach($asset_ids as $aid) {
					$sth = Ode_DBO::getInstance()->prepare("UPDATE " . DBO_Asset::TABLE_NAME . " SET photographer_id = :pid, modified_by = :user WHERE id = :id");
					$sth->bindParam(":pid", $photog_id, PDO::PARAM_INT, 11);
					$sth->bindParam(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR, 50);
					$sth->bindParam(":id", $aid, PDO::PARAM_STR, 50);
					
					try {
						$sth->execute();
					} catch(PDOException $e) {
						error_log($e->getTraceAsString(), 0);
					}
				}
				
				Ode_DBO::getInstance()->commit();
				
				Util::json($_POST);
				exit();
				break;
			case 'add':
				$first_name = trim($_POST['first_name']);
				$last_name = trim($_POST['last_name']);
				$asset_ids = $_POST['asset_ids'];
				
				Ode_DBO::getInstance()->beginTransaction();
				
				$sth = Ode_DBO::getInstance()->prepare("
					INSERT INTO " . DBO_Photographer::TABLE_NAME . " (firstname, lastname, modified_by) 
					VALUES (:firstname, :lastname, :user)
				");
				$sth->bindParam(":firstname", $first_name, PDO::PARAM_STR, 50);
				$sth->bindParam(":lastname", $last_name, PDO::PARAM_STR, 50);
				$sth->bindParam(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR, 50);
				
				try {
					$sth->execute();
				} catch(PDOException $e) {
					error_log($e->getTraceAsString(), 0);
				}
				
				$new_id = Ode_DBO::getInstance()->lastInsertId();
				
				unset($sth);
				
				foreach($asset_ids as $aid) {
					$sth = Ode_DBO::getInstance()->prepare("UPDATE " . DBO_Asset::TABLE_NAME . " SET photographer_id = :photog, modified = NOW(), modified_by = :user WHERE id = :id");
					$sth->bindParam(":photog", $new_id, PDO::PARAM_INT, 11);
					$sth->bindParam(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR, 50);
					$sth->bindParam(":id", $aid, PDO::PARAM_STR, 50);
					
					try {
						$sth->execute();
					} catch(PDOException $e) {
						error_log($e->getTraceAsString(), 0);
					}
				}
				
				Ode_DBO::getInstance()->commit();
				
				Util::json($_POST);
				exit();
				break;
		}
		break;
	case 'grp':
		switch(Ode_Manager::getInstance()->getTask()) {
			default:
				
				break;
			case 'add':
				$assets = DBO_Asset::getAllByGroup($_POST['gid']);
				
				Ode_DBO::getInstance()->beginTransaction();
				
				foreach($assets as $asset) {
					$sth = Ode_DBO::getInstance()->prepare("
						UPDATE " . DBO_Asset::TABLE_NAME . "
						SET
							photographer_id = :photog,
							modified_by = :user,
							modified = NOW()
						WHERE id = :id
					");
					$sth->bindValue(":photog", $_POST['pid'], PDO::PARAM_INT);
					$sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
					$sth->bindValue(":id", $asset->id, PDO::PARAM_STR);
					
					try {
						$sth->execute();
					} catch(PDOException $e) {
						//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
					}
				}
				
				Ode_DBO::getInstance()->commit();
				break;
		}
		break;
	case 'group':
		switch(Ode_Manager::getInstance()->getTask()) {
			default: break;
			case 'assign':
				$assets = DBO_Asset::getAllByGroup($_POST['_gid']);
				$photog = DBO_Photographer::getOneById($_POST['_id']);
				
				Ode_DBO::getInstance()->beginTransaction();
				
				foreach($assets as $asset) {
					$sth = Ode_DBO::getInstance()->prepare("
						UPDATE " . DBO_Asset::TABLE_NAME . "
						SET
							photographer_id = :photog,
							modified_by = :user,
							modified = NOW()
						WHERE id = :id
					");
					$sth->bindValue(":photog", $photog->id, PDO::PARAM_INT);
					$sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
					$sth->bindValue(":id", $asset->id, PDO::PARAM_STR);
						
					try {
						$sth->execute();
					} catch(PDOException $e) {
						//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
					}
				}
				
				Ode_DBO::getInstance()->commit();
				
				Util::json($photog);
				exit();
				break;
			case 'add':
				$assets = DBO_Asset::getAllByGroup($_POST['_gid']);
				
				Ode_DBO::getInstance()->beginTransaction();
				
				$sth = Ode_DBO::getInstance()->prepare("
					INSERT INTO " . DBO_Photographer::TABLE_NAME . " (firstname, lastname, modified_by)
					VALUES (:fname, :lname, :user)
				");
				$sth->bindValue(":fname", trim($_POST['addPhotogFname']), PDO::PARAM_STR);
				$sth->bindValue(":lname", trim($_POST['addPhotogLname']), PDO::PARAM_STR);
				$sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
				
				try {
					$sth->execute();
				} catch(PDOException $e) {
					//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                    error_log($e->getMessage(), 0);
				}
				
				unset($sth);
				
				$pid = Ode_DBO::getInstance()->query("SELECT LAST_INSERT_ID()")->fetchColumn();
				
				foreach($assets as $asset) {
					$sth = Ode_DBO::getInstance()->prepare("
						UPDATE " . DBO_Asset::TABLE_NAME . "
						SET
							photographer_id = :photog,
							modified_by = :user,
							modified = NOW()
						WHERE id = :id
					");
					$sth->bindValue(":photog", $pid, PDO::PARAM_INT);
					$sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
					$sth->bindValue(":id", $asset->id, PDO::PARAM_STR);
				
					try {
						$sth->execute();
					} catch(PDOException $e) {
						//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
					}
				}
				
				Ode_DBO::getInstance()->commit();
				
				Util::json($_POST);
				exit();
				break;
		}
		break;
	case 'asset':
		switch(Ode_Manager::getInstance()->getTask()) {
			default: break;
			case 'assign':
				$sth = Ode_DBO::getInstance()->prepare("
					UPDATE " . DBO_Asset::TABLE_NAME . "
					SET
						photographer_id = :photog,
						modified = NOW(),
						modified_by = :user
					WHERE id = :id
				");
				$sth->bindValue(":photog", $_POST['_id'], PDO::PARAM_INT);
				$sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
				$sth->bindValue(":id", $_POST['_aid'], PDO::PARAM_STR);
				
				try {
					$sth->execute();
				} catch(PDOException $e) {
					//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                    error_log($e->getMessage(), 0);
				}
				
				$photog = DBO_Photographer::getOneById($_POST['_id']);
				
				Util::json($photog);
				exit();
				break;
			case 'add':
				Ode_DBO::getInstance()->beginTransaction();
				
				$sth = Ode_DBO::getInstance()->prepare("
					INSERT INTO " . DBO_Photographer::TABLE_NAME . " (firstname, lastname, modified_by)
					VALUES (:fname, :lname, :user)
				");
				$sth->bindValue(":fname", trim($_POST['addPhotogFname']), PDO::PARAM_STR);
				$sth->bindValue(":lname", trim($_POST['addPhotogLname']), PDO::PARAM_STR);
				$sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
				
				try {
					$sth->execute();
				} catch(PDOException $e) {
					//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                    error_log($e->getMessage(), 0);
				}
				
				unset($sth);
				
				$newId = Ode_DBO::getInstance()->query("SELECT LAST_INSERT_ID()")->fetchColumn();
				
				$sth = Ode_DBO::getInstance()->prepare("
					UPDATE " . DBO_Asset::TABLE_NAME . "
					SET
						photographer_id = :pid,
						modified = NOW(),
						modified_by = :user
					WHERE id = :id
				");
				$sth->bindValue(":pid", $newId, PDO::PARAM_INT);
				$sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
				$sth->bindValue(":id", $_POST['_aid'], PDO::PARAM_STR);
				
				try {
					$sth->execute();
				} catch(PDOException $e) {
					//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                    error_log($e->getMessage(), 0);
				}
				
				Ode_DBO::getInstance()->commit();
				
				Util::json($_POST);
				exit();
				break;
			case 'edit':
				$sth = Ode_DBO::getInstance()->prepare("
					UPDATE " . DBO_Photographer::TABLE_NAME . "
					SET
						firstname = :fname,
						lastname = :lname
					WHERE id = :id
				");
				$sth->bindValue(":fname", trim($_POST['editPhotogFname']), PDO::PARAM_STR);
				$sth->bindValue(":lname", trim($_POST['editPhotogLname']), PDO::PARAM_STR);
				$sth->bindValue(":id", $_POST['_id'], PDO::PARAM_INT);
				
				try {
					$sth->execute();
				} catch(PDOException $e) {
					//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                    error_log($e->getMessage(), 0);
				}
				
				Util::json($_POST);
				exit();
				break;
			case 'get':
				/*$photog = Ode_DBO::getInstance()->query("
					SELECT " . DBO_Asset::COLUMNS . "
					FROM " . DBO_Asset::TABLE_NAME . " AS b
					LEFT JOIN " . DBO_Photographer::TABLE_NAME . " AS a ON (b.photographer_id = a.id)
					WHERE b.id = " . Ode_DBO::getInstance()->quote($_POST['_aid'], PDO::PARAM_STR) . "
					LIMIT 0,1
				")->fetchObject();*/
				$photog = Ode_DBO::getInstance()->query("
					SELECT " . DBO_Photographer::SELECT_COLUMNS . "
					FROM " . DBO_Photographer::TABLE_NAME . " AS a
					LEFT JOIN " . DBO_Asset::TABLE_NAME . " AS b ON (b.photographer_id = a.id)
					WHERE b.id = " . Ode_DBO::getInstance()->quote($_POST['_aid'], PDO::PARAM_STR) . "
					LIMIT 0,1
				")->fetchObject();
				
				//Util::json($photog);
				Ode_View::getInstance()->assign("photographer", $photog);
				
				echo Ode_View::getInstance()->fetch("ajax/assets/edit_photog_form.tpl.php");
				exit();
				break;
		}
		break;
	case 'all':
		$photogs = Ode_DBO::getInstance()->query("
			SELECT " . DBO_Photographer::SELECT_COLUMNS . "
			FROM " . DBO_Photographer::TABLE_NAME . " AS a
			ORDER BY a.lastname
			ASC
		")->fetchAll(PDO::FETCH_CLASS, DBO_Photographer::MODEL_NAME);
		
		Ode_View::getInstance()->assign("photographers", $photogs);
		echo Ode_View::getInstance()->fetch("ajax/photographersList.tpl.php");
		exit();
		break;
}

?>