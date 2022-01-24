<?php
switch(Ode_Manager::getInstance()->getMode()) {
	default:
		$org_ids = null;
		
		if($_POST['orgs'] != "") {
			$org_ids = $_POST['orgs'];
			$org_ids = explode(',', $org_ids);
		}
		
		if(empty($org_ids)) {
			$cnxs = DBO_User_Organization_Cnx::getAllByUser($_POST['uid']);
			$org_ids = array();
			foreach($cnxs as $cnx) {
				$org_ids[] = $cnx->org_id;
			}
		}
        //Util::debug($org_ids); die();
		
		$uploader = new UCI_ImageArchive_Upload(IMAGE_STORAGE_PATH, "file");
		$uploader->process();
		
		$uuid = DBO_Asset::addFullUpload($uploader->getDBFilename(), $uploader->getMimeType(), $_POST['uid']);
		
		/**
		 * add asset to its assigned organizations
		 */
		foreach ($org_ids as $org_id) {
			DBO_Asset_Metadata::add(DBO_Asset_Metadata::META_ORG_ID_NAME, $org_id, $uuid);
		}
		
		//echo $json->encode(array('id' => $uuid));
        echo json_encode(array('id' => $uuid));
				
		$_SESSION[SESSION_UPLOAD_NAME][] = $uuid;
		
		exit();
		break;
	case 'grp':
		$uploader = new UCI_ImageArchive_Upload(IMAGE_STORAGE_PATH, "file");
		$uploader->process();
		
		/**
		 *
		 * Add instance of file to database for reference
		 * @var PDOStatement
		 */
		DBO_Group::addUpload($uploader->getDBFilename(), $uploader->getMimeType(), $_POST['uid'], $_POST['gid']);
		
		//echo $json->encode(1);
        echo json_encode(1);
		
		exit();
		break;
}
?>