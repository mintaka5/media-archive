<?php
/**
 *
 * Reset current group, because we are not coming from a group view
 * @var string
 */
$_SESSION[CURRENT_GROUP_VAR] = null;

SearchManager::getInstance()->setTerms($_GET['terms']);
/**
 * @todo set term variable below to SessionManager variables instead of $_GET variable
 */

switch(Ode_Manager::getInstance()->getMode()) {
    default:
        switch (Ode_Manager::getInstance()->getTask()) {
            default:
                $query = trim($_GET['terms']);
                $query = "%" . preg_replace("/[\s\W\t\r\n]+/", "%", $query) . "%";
                
                $sql = "SELECT " . DBO_Asset::COLUMNS . "
		                FROM " . DBO_Asset::TABLE_NAME . " AS a
		                LEFT JOIN " . DBO_Keyword_Asset_Cnx::TABLE_NAME . " AS b ON (b.asset_id = a.id)
		                LEFT JOIN " . DBO_Keyword::TABLE_NAME . " AS c ON (c.id = b.keyword_id)
		                LEFT JOIN " . DBO_Asset_Restriction_Embargo::TABLE_NAME . " AS d ON (d.asset_id = a.id)
		                LEFT JOIN " . DBO_Asset_Restriction_External::TABLE_NAME . " AS e ON (e.asset_id = a.id)
		                LEFT JOIN " . DBO_Asset_Restriction_Internal::TABLE_NAME . " AS f ON (f.asset_id = a.id)
		                LEFT JOIN " . DBO_Asset_Restriction_Subject::TABLE_NAME . " AS g ON (g.asset_id = a.id)
		               	LEFT JOIN " . DBO_Asset_Restriction_Hippa::TABLE_NAME . " AS h ON (h.asset_id = a.id)
		               	LEFT JOIN " . DBO_Asset_Restriction_NCAA::TABLE_NAME . " AS i ON (i.asset_id = a.id)
		                WHERE a.is_active = 1
		                AND a.is_deleted = 0
		                AND (
		                	c.keyword LIKE " . Ode_DBO::getInstance()->quote($query, PDO::PARAM_STR) . "
		                	OR a.title LIKE " . Ode_DBO::getInstance()->quote($query, PDO::PARAM_STR) . "
		                	OR a.caption LIKE " . Ode_DBO::getInstance()->quote($query, PDO::PARAM_STR) . "
		                	OR a.description LIKE " . Ode_DBO::getInstance()->quote($query, PDO::PARAM_STR) . "
		                	OR a.credit LIKE " . Ode_DBO::getInstance()->quote($query, PDO::PARAM_STR) . "
		                )
		                AND (d.start_date IS NULL OR d.start_date < NOW())
		                AND e.id IS NULL
		                AND f.id IS NULL
		                AND g.id IS NULL
		                AND h.id IS NULL
		                AND i.id IS NULL
		                GROUP BY a.id";
                //echo $sql;
                
                $results = Ode_DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_CLASS, DBO_Asset::MODEL_NAME);
                
                $pager = Pager::factory(array(
                        'perPage' => 12,
                        'urlVar' => "page",
                        'mode' => "Sliding",
                        'delta' => 5,
                        'itemData' => $results
                ));
                
                Ode_View::getInstance()->assign("assets", $pager->getPageData());
                Ode_View::getInstance()->assign("pagelinks", $pager->getLinks());
                break;
        }
        break;
    case 'kwords':
    	switch(Ode_Manager::getInstance()->getTask()) {
    		default:
    			$asset = DBO_Asset::getOneByPublicId(trim($_GET['id']));
    			
    			$sql = "SELECT " . DBO_Asset::COLUMNS . "
    					FROM " . DBO_Keyword::TABLE_NAME . " AS b
    					LEFT JOIN " . DBO_Keyword_Asset_Cnx::TABLE_NAME . " AS c ON (c.keyword_id = b.id)
    					LEFT JOIN " . DBO_Asset::TABLE_NAME . " AS a ON (a.id = c.asset_id)
    					LEFT JOIN " . DBO_Asset_Restriction_Embargo::TABLE_NAME . " AS d ON (d.asset_id = a.id)
		                LEFT JOIN " . DBO_Asset_Restriction_External::TABLE_NAME . " AS e ON (e.asset_id = a.id)
		                LEFT JOIN " . DBO_Asset_Restriction_Internal::TABLE_NAME . " AS f ON (f.asset_id = a.id)
		                LEFT JOIN " . DBO_Asset_Restriction_Subject::TABLE_NAME . " AS g ON (g.asset_id = a.id)
		               	LEFT JOIN " . DBO_Asset_Restriction_Hippa::TABLE_NAME . " AS h ON (h.asset_id = a.id)
		               	LEFT JOIN " . DBO_Asset_Restriction_NCAA::TABLE_NAME . " AS i ON (i.asset_id = a.id)
    					WHERE b.keyword IN (" . $asset->viewKeywords("'") . ")
    					AND a.is_active = 1
    					AND a.is_deleted = 0
    					AND (d.start_date IS NULL OR d.start_date < NOW())
		                AND e.id IS NULL
		                AND f.id IS NULL
		                AND g.id IS NULL
		                AND h.id IS NULL
		                AND i.id IS NULL
    					GROUP BY a.id";
    			//echo $sql;
    			
    			$results = Ode_DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_CLASS, DBO_Asset::MODEL_NAME);
    			
    			$pager = Pager::factory(array(
    				'perPage' => 40,
    			    'urlVar' => "page",
    			    'mode' => "Sliding",
    			    'append' => true,
    			    'path' => Ode_Manager::getInstance()->action("search", "kwords", null, array("id", $asset->public_id)),
    			    'delta' => 5,
    			    'itemData' => $results
    			));
    			
    			Ode_View::getInstance()->assign("asset", $asset);
    			Ode_View::getInstance()->assign("assets", $pager->getPageData());
    			Ode_View::getInstance()->assign("pagelinks", $pager->getLinks());
    			break;
    	}
    	break;
}
?>
