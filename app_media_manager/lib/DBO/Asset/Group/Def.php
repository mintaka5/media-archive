<?php
class DBO_Asset_Group_Def {
	public static function getOneByCnxId($cnx_id) {
		return Ode_DBO::getInstance()->query("
			SELECT def.*
			FROM asset_group_def AS def
			WHERE def.cnx_id = " . Ode_DBO::getInstance()->quote($cnx_id, PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchObject("DBO_Asset_Group_Def_Model");
	}
	
	public static function removeByCnx($cnx_id) {
		$sth = Ode_DBO::getInstance()->prepare("DELETE FROM asset_group_def WHERE cnx_id = :cnx_id");
		$sth->bindValue(":cnx_id", $cnx_id, PDO::PARAM_INT);
		
		try {
			$sth->execute();
		} catch(PDOException $e) {
			//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
            error_log($e->getMessage(), 0);
		}
	}
}