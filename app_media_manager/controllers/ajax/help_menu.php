<?php
require_once './init.php';

$doc = new DOMDocument("1.0", "UTF-8");
$rootNode = $doc->createElement("root");
$doc->appendChild($rootNode);

$categories = Ode_DBO::getInstance()->query("
	SELECT a.*
	FROM " . FAQ_DATABASE_NAME . ".faqcategories AS a
	ORDER BY a.name
	ASC
")->fetchAll(PDO::FETCH_OBJ);

foreach($categories as $category) {
	$itemNode = $doc->createElement("item");
	$itemNode->setAttribute("id", "node_".$category->id);
	
	$contentNode = $doc->createElement("content");
	$itemNode->appendChild($contentNode);
	$nameNode = $doc->createElement("name");
	$contentNode->appendChild($nameNode);
	
	if($category->parent_id <= 0) { // top level node
		$itemNode->setAttribute("parent_id", 0);
	} else {
		$itemNode->setAttribute("parent_id", "node_".$category->parent_id);
	}
	
	$nameCdata = $doc->createCDATASection($category->name);
	$nameNode->appendChild($nameCdata);
	
	$rootNode->appendChild($itemNode);
}

header("Content-Type: text/xml");
echo $doc->saveXML();

exit();
?>