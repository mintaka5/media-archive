<?php
require_once './init.php';

switch(Ode_Manager::getInstance()->getMode()) {
	default:
		switch(Ode_Manager::getInstance()->getTask()) {
			default:
				Ode_DBO::getInstance()->setAttribute(PDO::ATTR_PERSISTENT, true);
				
				$keywords = Ode_DBO::getInstance()->query("
					SELECT " . DBO_Keyword::COLUMNS . "
					FROM " . DBO_Keyword::TABLE_NAME . " AS a
					WHERE a.is_deleted = 0
					ORDER BY a.keyword
					ASC
				")->fetchAll(PDO::FETCH_CLASS, DBO_Keyword::MODEL_NAME);
				
				$pager = Pager::factory(array(
					'perPage' => 24,
					'urlVar' => "pageNum",
					'mode' => "Sliding",
					'append' => false,
					'path' => "",
					'fileName' => "javascript:keywordsList(%d);",
					'delta' => 4,
					'itemData' => $keywords
				));
				
				Ode_View::getInstance()->assign("keywords", $pager->getPageData());
				Ode_View::getInstance()->assign("kwordlinks", $pager->getLinks());
				
				Ode_DBO::getInstance()->setAttribute(PDO::ATTR_PERSISTENT, false);
				
				header("Content-Type: text/html");
				echo Ode_View::getInstance()->fetch("ajax/kwordsList.tpl.php");
				exit();
				break;
			case 'del':
				//DBO_Keyword::setDeleted($_POST['id']);
				DBO_Keyword::delete($_POST['id']);
				break;
			case 'add':
				$keyword = trim($_POST['kword']);
				
				DBO_Keyword::add($keyword);
				
				Util::json($keyword);
				exit();
				break;
			case 'edit':
				$keyword = trim($_POST['kword']);
				$id = trim($_POST['id']);
				
				$sth = Ode_DBO::getInstance()->prepare("UPDATE " . DBO_Keyword::TABLE_NAME . " SET keyword = :keyword WHERE id = :id");
				$sth->bindParam(":keyword", $keyword, PDO::PARAM_STR, 45);
				$sth->bindParam(":id", $id, PDO::PARAM_INT, 11);
				
				try {
					$sth->execute();
				} catch(PDOException $e) {
					//Ode_Error::mail($e->getMessage(), __LINE__, __FILE__, APP_ADMIN_EMAIL);
                    error_log($e->getMessage(), 0);

					Util::json(false); exit();
				} catch(Exception $e) {
					//Ode_Error::mail($e->getMessage(), __LINE__, __FILE__, APP_ADMIN_EMAIL);
                    error_log($e->getMessage(), 0);

					Util::json(false); exit();
				}
				
				Util::json($_POST);
				exit();
				break;
		}
		break;
	case 'suggest':
		switch(Ode_Manager::getInstance()->getTask()) {
			default:
				$qry = "%" . preg_replace("/[\r\n\s\t\W]+/", "%", trim($_POST['q'])) . "%";
				
				$keywords = Ode_DBO::getInstance()->query("
					SELECT " . DBO_Keyword::COLUMNS . "
					FROM " . DBO_Keyword::TABLE_NAME . " AS a
					WHERE a.keyword LIKE " . Ode_DBO::getInstance()->quote($qry, PDO::PARAM_STR) . "
					AND a.is_deleted = 0
					ORDER BY a.keyword
					ASC
				")->fetchAll(PDO::FETCH_ASSOC);
				
				Util::json($keywords);
				exit();
				break;
			case 'html':
				$qry = "%" . preg_replace("/[\r\n\s\t\W]+/", "%", trim($_POST['q'])) . "%";
				
				$keywords = Ode_DBO::getInstance()->query("
						SELECT " . DBO_Keyword::COLUMNS . "
						FROM " . DBO_Keyword::TABLE_NAME . " AS a
						WHERE a.keyword LIKE " . Ode_DBO::getInstance()->quote($qry, PDO::PARAM_STR) . "
						AND a.is_deleted = 0
						ORDER BY a.keyword
						ASC
						")->fetchAll(PDO::FETCH_CLASS, DBO_Keyword::MODEL_NAME);
				
				$pager = Pager::factory(array(
						'perPage' => 24,
						'urlVar' => "pageNum",
						'mode' => "Sliding",
						'append' => false,
						'path' => "",
						'fileName' => "javascript:keywordsList(%d);",
						'delta' => 4,
						'itemData' => $keywords
				));
				
				Ode_View::getInstance()->assign("keywords", $pager->getPageData());
				Ode_View::getInstance()->assign("kwordlinks", $pager->getLinks());
				
				header("Content-Type: text/html");
				echo Ode_View::getInstance()->fetch("ajax/kwordsList.tpl.php");
				exit();
				break;
		}
		break;
	case 'asset':
		switch(Ode_Manager::getInstance()->getTask()) {
			default:
				
				break;
			case 'add':
				DBO_Keyword_Asset_Cnx::assign($_POST['_id'], $_POST['_tag']);
				
				Util::json($_POST);
				exit();
				break;
			case 'del':
				DBO_Keyword_Asset_Cnx::unassign($_POST['_id'], $_POST['_tag']);
				
				Util::json($_POST);
				exit();
				break;
		}
		break;
	case 'new':
		switch (Ode_Manager::getInstance()->getTask()) {
			default:
				$newId = DBO_Keyword::add($_POST['kword']);
			
				DBO_Keyword_Asset_Cnx::assignById($_POST['aid'], $newId);
				
				Util::json(array("formdata" => $_POST, "keyword_id" => $newId));
				exit();
				break;
			case 'grp':
				
				break;
		}
		break;
	case 'get':
		switch(Ode_Manager::getInstance()->getTask()) {
			default:break;
			case 'one':
				$keyword = trim($_POST['kword']);
				
				$exists = DBO_Keyword::getOneByKeyword($keyword);
				
				if($exists != false) {
					Util::json($keyword);
					exit();
				}
				
				Util::json(false);
				break;
			case 'byid':
				$id = trim($_POST['id']);
				$keyword = Ode_DBO::getInstance()->query("
					SELECT " . DBO_Keyword::COLUMNS . "
					FROM " . DBO_Keyword::TABLE_NAME . " AS a
					WHERE a.id = " . Ode_DBO::getInstance()->quote($id, PDO::PARAM_INT) . "
					LIMIT 0,1
				")->fetch(PDO::FETCH_ASSOC);
				
				Util::json($keyword);
				exit();
				break;
		}
		break;
}
?>