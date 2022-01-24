<?php
require_once './init.php';

$group = Ode_DBO::getInstance()->query("
	SELECT " . DBO_Group::COLUMNS . "
	FROM " . DBO_Group::TABLE_NAME . " AS a
	WHERE a.id = " . Ode_DBO::getInstance()->quote($_POST['_gid'], PDO::PARAM_STR) . "
	LIMIT 0,1
")->fetchObject(DBO_Group::MODEL_NAME);

switch(Ode_Manager::getInstance()->getMode()) {
	default:
		
		break;
	case 'kwords':
		
		break;
	case 'status':
		Ode_DBO::getInstance()->beginTransaction();
		
		$assets = $group->assets();
		foreach($assets as $asset) {
			$sth = Ode_DBO::getInstance()->prepare("
				UPDATE assets
				SET
					is_approved = :is_approved
				WHERE id = :id
			");
			$sth->bindValue(":is_approved", $_POST['_stat'], PDO::PARAM_INT);
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
exit();
?>