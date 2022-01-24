<?php
class ContainerTest extends PHPUnit_Extensions_Database_TestCase {
	
	
	public function getConnection() {
		$pdo = new PDO('mysql:host=cwisdb2.cwis.uci.edu;dbname=dev-image-archive', 'image_archive', '1m@g3_r39o');
		return $this->createDefaultDBConnection($pdo, 'dev-image-archive');
	}
	
	public function getDataSet() {
		return $this->createMySQLXMLDataSet(dirname(__FILE__) . '/image_archive.xml');
	}
	
	public function testByUserOrgs() {
		$actual = $this->getConnection()->createQueryTable('byUserOrgsQuery', "SELECT a.*
					FROM containers AS a
					LEFT JOIN container_metadata AS b ON (b.container_id = a.id)
					WHERE a.is_deleted = 0
					AND b.meta_key = 'org_id'
					AND b.meta_value IN (3)
					GROUP BY a.id");
		
		$expected = $this->createXMLDataSet('byUserOrgsQueryAssertion.xml')->getTable('byUserOrgsQuery');
		
		$this->assertTablesEqual($expected, $actual);
	}
}
?>