<?php

switch(Ode_Manager::getInstance()->getMode()) {
	default:
		/**
		 * 
		 * Featured image
		 * @var DBO_Asset_Model
		 */
		$featuredImage = Ode_DBO::getInstance()->query("
			SELECT " . DBO_Asset::COLUMNS . "
			FROM " . DBO_Asset::TABLE_NAME . " AS a
			LEFT JOIN " . DBO_Properties::TABLE_NAME. " AS b ON (b.value = a.id)
			WHERE b.machine_name = 'featured_image'
		")->fetchObject(DBO_Asset::MODEL_NAME);
		
		/**
		 * 
		 * Featured groups
		 * @var DBO_Group_Model
		 */
		$json = new Services_JSON();
		$grpIdsStr = Ode_DBO::getInstance()->query("
			SELECT a.value
			FROM " . DBO_Properties::TABLE_NAME . " AS a
			WHERE a.is_enabled = 1
			AND a.machine_name = 'featured_groups'
			LIMIT 0,1
		")->fetchColumn();
		$grpIds = $json->decode($grpIdsStr);
		$inStr = Util::dbQuoteListItems($grpIds);
		$inStr = implode(", ", $inStr);
		
		$featuredGrps = false;
		if(!is_null($inStr)) {
			$featuredGrps = Ode_DBO::getInstance()->query("
				SELECT " . DBO_Group::COLUMNS . "
				FROM " . DBO_Group::TABLE_NAME . " AS a
				WHERE a.id IN (" . $inStr . ")
				AND a.is_approved = 1
				AND a.is_deleted = 0
				LIMIT 0,12
			")->fetchAll(PDO::FETCH_CLASS, DBO_Group::MODEL_NAME);
		}
		
		/**
		 * Grab the top most viewd images
		 */
		$most_viewed = Ode_DBO::getInstance()->query("SELECT * 
			FROM view_publicAssetsMostViewed 
			LIMIT 0,4"
		);

        /*
         * handle an empty query if no featured image is set
         * @todo handle this better!
         */
        if(!empty($most_viewed)) {
            $most_viewed = $most_viewed->fetchAll(PDO::FETCH_CLASS, DBO_Asset::MODEL_NAME);
        }
	
		Ode_View::getInstance()->assign("mostviewed", $most_viewed);
		Ode_View::getInstance()->assign("featuredgroups", $featuredGrps);
		Ode_View::getInstance()->assign("featuredimage", $featuredImage);

		break;
}
?>