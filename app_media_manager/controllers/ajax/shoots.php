<?php
require_once './init.php';

switch(Ode_Manager::getInstance()->getMode()) {
	default:
		
		break;
	case 'remove':
		$asset_id = $_POST['asset_id'];
		
		$sth = Ode_DBO::getInstance()->prepare("UPDATE " . DBO_Asset::TABLE_NAME . " SET shoot_id = NULL WHERE id = :id");
		$sth->bindParam(":id", $asset_id, PDO::PARAM_STR, 50);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			//Ode_Error::mail($e->getMessage(), __LINE__, __FILE__, APP_ADMIN_EMAIL);
            error_log($e->getMessage(), 0);
		} catch(Exception $e) {
			//Ode_Error::mail($e->getMessage(), __LINE__, __FILE__, APP_ADMIN_EMAIL);
            error_log($e->getMessage(), 0);
		}
		
		Util::json($_POST);
		exit();
		break;
	case 'edit':
		$sth = Ode_DBO::getInstance()->prepare("
			UPDATE " . DBO_Shoot::TABLE_NAME . "
			SET
				title = :title,
				description = :desc,
				shoot_date = :date,
				modified = NOW(),
				modified_by = :user
			WHERE id = :id
		");
		$sth->bindValue(":title", trim($_POST['editShootTitle']), PDO::PARAM_STR);
		$sth->bindValue(":desc", trim($_POST['eShootDesc']), PDO::PARAM_STR);
		$sth->bindValue(":date", date("Y-m-d H:i:s", strtotime(trim($_POST['editShootDate']))), PDO::PARAM_STR);
		$sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
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
	case 'add':
		Ode_DBO::getInstance()->beginTransaction();
		
		$sth = Ode_DBO::getInstance()->prepare("
			INSERT INTO shoots (shoot_name, title, description, shoot_date, is_active, created, modified, modified_by)
			VALUES (:name, :title, :desc, :date, 1, NOW(), NOW(), :user)
		");
		$name = strtolower(trim($_POST['shootTitle']));
		$name = preg_replace("/[\s\W]+/", "", $name);
		$name = substr($name, 0, 10) . rand(100, 1000000);
		$sth->bindValue(":name", $name, PDO::PARAM_STR);
		$sth->bindValue(":title", trim($_POST['shootTitle']), PDO::PARAM_STR);
		$sth->bindValue(":desc", trim($_POST['shootDesc']), PDO::PARAM_STR);
		$sth->bindValue(":date", date("Y-m-d H:i:s", strtotime(trim($_POST['shootDate']))), PDO::PARAM_STR);
		$sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
            error_log($e->getMessage(), 0);
		}
		
		$newId = Ode_DBO::getInstance()->query("SELECT LAST_INSERT_ID()")->fetchColumn();
		
		$sth = Ode_DBO::getInstance()->prepare("
			UPDATE " . DBO_Asset::TABLE_NAME . "
			SET 
				shoot_id = :shoot,
				modified_by = :user
			WHERE id = :id
		");
		$sth->bindValue(":shoot", $newId, PDO::PARAM_INT);
		$sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
		$sth->bindValue(":id", $_POST['_id'], PDO::PARAM_STR);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
            error_log($e->getMessage(), 0);
		}
		
		Ode_DBO::getInstance()->commit();
		
		$asset = DBO_Asset::getOneById($_POST['_id']);
		
		Util::json(array("formdata" => $_POST, "shoot_title" => $asset->shoot()->title));
		exit();
		break;
	case 'update':
		$sth = Ode_DBO::getInstance()->prepare("
			UPDATE assets
			SET 
				shoot_id = :shoot,
				modified = NOW(),
				modified_by = :user
			WHERE id = :id
		");
		$sth->bindValue(":shoot", $_POST['_sid'], PDO::PARAM_INT);
		$sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
		$sth->bindValue(":id", $_POST['_aid'], PDO::PARAM_STR);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
            error_log($e->getMessage(), 0);
		}
		
		$asset = DBO_Asset::getOneById($_POST['_aid']);
		
		Util::json(array("formadata" => $_POST, "shoot_title" => $asset->shoot()->title));
		exit();
		break;
	case 'grp':
		switch(Ode_Manager::getInstance()->getTask()) {
			case 'add':
				$assets = DBO_Asset::getAllByGroup($_POST['gid']);
				
				Ode_DBO::getInstance()->beginTransaction();
				
				foreach($assets as $asset) {
					$sth = Ode_DBO::getInstance()->prepare("
						UPDATE " . DBO_Asset::TABLE_NAME . "
						SET
							shoot_id = :shoot,
							modified_by = :user,
							modified = NOW()
						WHERE id = :id
					");
					$sth->bindValue(":shoot", $_POST['sid'], PDO::PARAM_INT);
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
	case 'list':
		switch(Ode_Manager::getInstance()->getTask()) {
			default:
				$shoots = Ode_DBO::getInstance()->query("
					SELECT " . DBO_Shoot::COLUMNS . "
					FROM " . DBO_Shoot::TABLE_NAME . " AS a
					WHERE a.is_active = 1
					ORDER BY a.title
					ASC
				")->fetchAll(PDO::FETCH_CLASS, DBO_Shoot::MODEL_NAME);
			
				Ode_View::getInstance()->assign("shoots", $shoots);
				echo Ode_View::getInstance()->fetch("ajax/shootsList.tpl.php");
				exit();
				break;
		}
		break;
	case 'asset':
		switch(Ode_Manager::getInstance()->getTask()) {
			default: break;
			case 'assign':
				$shoot = DBO_Shoot::getOneById($_POST['_id']);
				
				$sth = Ode_DBO::getInstance()->prepare("
					UPDATE " . DBO_Asset::TABLE_NAME . "
					SET
						shoot_id = :shoot,
						modified = NOW(),
						modified_by = :user
					WHERE id = :id
				");
				$sth->bindValue(":shoot", $_POST['_id'], PDO::PARAM_INT);
				$sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
				$sth->bindValue(":id", $_POST['_aid'], PDO::PARAM_STR);
				
				try {
					$sth->execute();
				} catch(PDOException $e) {
					//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                    error_log($e->getMessage(), 0);
				}
				
				Util::json(array('formdata' => $_POST, 'title' => $shoot->title));
				break;
		}
		break;
	case 'group':
		switch(Ode_Manager::getInstance()->getTask()) {
			default: break;
			case 'assign':
				$assets = DBO_Asset::getAllByGroup($_POST['gid']);
				$shoot = DBO_Shoot::getOneById($_POST['sid']);
				
				Ode_DBO::getInstance()->beginTransaction();
				
				foreach($assets as $asset) {
					$sth = Ode_DBO::getInstance()->prepare("
							UPDATE " . DBO_Asset::TABLE_NAME . "
							SET
							shoot_id = :shoot,
							modified = NOW(),
							modified_by = :user
							WHERE id = :id
							");
					$sth->bindValue(":shoot", $shoot->id, PDO::PARAM_INT);
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
				
				Util::json($shoot->title);
				exit();
				break;
			case 'add':
				$assets = DBO_Asset::getAllByGroup($_POST['gid']);
				
				Ode_DBO::getInstance()->beginTransaction();
				
				$name = strtolower(trim($_POST['shootTitle']));
				$name = preg_replace("/[\s\W]+/", "", $name);
				$name = substr($name, 0, 10) . rand(100, 1000000);
				
				$sth = Ode_DBO::getInstance()->prepare("
					INSERT INTO " . DBO_Shoot::TABLE_NAME . " (shoot_name, title, description, shoot_date, created, modified, modified_by)
					VALUES (:name, :title, :desc, :date, NOW(), NOW(), :user)
				");
				$sth->bindValue(":name", $name, PDO::PARAM_STR);
				$sth->bindValue(":title", trim($_POST['shootTitle']), PDO::PARAM_STR);
				$sth->bindValue(":desc", trim($_POST['shootDesc']), PDO::PARAM_STR);
				$sth->bindValue(":date", date("Y-m-d H:i:s", strtotime(trim($_POST['shootDate']))), PDO::PARAM_STR);
				$sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
				
				try {
					$sth->execute();
				} catch(PDOException $e) {
					//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                    error_log($e->getMessage(), 0);
				}
				
				$sid = Ode_DBO::getInstance()->query("SELECT LAST_INSERT_ID()")->fetchColumn();
				
				unset($sth);
				
				foreach($assets as $asset) {
					$sth = Ode_DBO::getInstance()->prepare("
						UPDATE " . DBO_Asset::TABLE_NAME . "
						SET
							shoot_id = :shoot,
							modified = NOW(),
							modified_by = :user
						WHERE id = :id
					");
					$sth->bindValue(":shoot", $sid, PDO::PARAM_INT);
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
	case 'get':
		switch(Ode_Manager::getInstance()->getTask()) {
			case 'one':
				$asset = DBO_Asset::getOneById($_POST['asset_id']);
								
				Ode_View::getInstance()->assign("asset", $asset);
				
				echo Ode_View::getInstance()->fetch("ajax/editAssetShoot.tpl.php");
				exit();
				break;
		}
		break;
	case 'batch':
		switch(Ode_Manager::getInstance()->getTask()) {
			default: break;
			case 'update':
				$shoot_id = $_POST['shoot_id'];
				$asset_ids = $_POST['asset_ids'];
				
				Ode_DBO::getInstance()->beginTransaction();
				
				foreach ($asset_ids as $aid) {
					$sth = Ode_DBO::getInstance()->prepare("
						UPDATE " . DBO_Asset::TABLE_NAME . "
						SET
							shoot_id = :shoot,
							modified = NOW(),
							modified_by = :user
						WHERE id = :id
					");
					$sth->bindValue(":shoot", $shoot_id, PDO::PARAM_INT);
					$sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
					$sth->bindValue(":id", $aid, PDO::PARAM_STR);
						
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
				$shoot_date = date("Y-m-d H:i:s", strtotime($_POST['shoot_date']));
				$shoot_title = trim($_POST['shoot_title']);
				$shoot_desc = trim($_POST['shoot_desc']);
				$asset_ids = $_POST['asset_ids'];
				
				Ode_DBO::getInstance()->beginTransaction();
				
				$name = strtolower($shoot_title);
				$name = preg_replace("/[\s\W]+/", "", $name);
				$name = substr($name, 0, 10) . rand(100, 1000000);
				
				$sth = Ode_DBO::getInstance()->prepare("
					INSERT INTO " . DBO_Shoot::TABLE_NAME . " (shoot_name, title, description, shoot_date, is_active, created, modified, modified_by)
					VALUES (:name, :title, :desc, :date, 1, NOW(), NOW(), :user)
				");
				$sth->bindValue(":name", $name, PDO::PARAM_STR);
				$sth->bindValue(":title", $shoot_title, PDO::PARAM_STR);
				$sth->bindValue(":desc", $shoot_desc, PDO::PARAM_STR);
				$sth->bindValue(":date", $shoot_date, PDO::PARAM_STR);
				$sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
				
				try {
					$sth->execute();
				} catch(PDOException $e) {
					error_log($e->getTraceAsString(), 0);
				}
				
				$sid = Ode_DBO::getInstance()->query("SELECT LAST_INSERT_ID()")->fetchColumn();
				
				unset($sth);
				
				foreach ($asset_ids as $aid) {
					$sth = Ode_DBO::getInstance()->prepare("
						UPDATE " . DBO_Asset::TABLE_NAME . "
						SET
							shoot_id = :shoot,
							modified = NOW(),
							modified_by = :user
						WHERE id = :id
					");
					$sth->bindValue(":shoot", $sid, PDO::PARAM_INT);
					$sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
					$sth->bindValue(":id", $aid, PDO::PARAM_STR);
					
					try {
						$sth->execute();
					} catch(PDOException $e) {
						error_log($e->getTraceAsString(), 0);
					}
				}
				
				Ode_DBO::getInstance()->commit();
				
				Util::json($shoot_title);
				exit();
				break;
		}
		break;
}
?>