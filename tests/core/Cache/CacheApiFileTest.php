<?php

/**
 * @group cache
 * @group framework
 */
class CacheApiFileTest extends TestUtils_TestCase {

	/**
	 * @var Cache_API_File
	 */
	protected $api;

	protected function setUp() {
		$this->api = Cache::getApi('File');
	}

	public function testGetting() {
		self::markTestIncomplete();
	}

	public function testGettingExpired() {
		self::markTestIncomplete();
	}

	public function testSettingCache() {
		self::markTestIncomplete();
	}

	public function testClear() {
		self::markTestIncomplete();
	}

	public function testClearTag() {
		self::markTestIncomplete();
	}

	public function testGarbage() {
		self::markTestIncomplete();
	}

	protected function tearDown() {
		$this->api = null;
	}

}