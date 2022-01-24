<?php
class AssetTest extends PHPUnit_Framework_TestCase {
	public function testTrueIsTrue() {
		$foo = true;
		$this->assertTrue($foo);
	}
	
	/**
	 * @dataProvider 
	 * @param unknown $asset_id
	 */
	public function testIsValidAsset($asset_id) {
		$asset = DBO_Asset::getOneById($asset_id);
		
		$this->assertInstanceOf(DBO_Asset_Model, $asset);
	}
	
	public function testValidAssetProvider() {
		
	}
}
?>