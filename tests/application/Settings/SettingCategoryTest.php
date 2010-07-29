<?php

class Settings_SettingCategiryTest extends TestUtils_TestCase {

	protected $names = array(
		  'category 01'
		, 'category 02'
		, 'category 03'
	);

	protected function setUp() {
		$this->invalidateCaches();
		Nano::db()->delete(Setting_Category::NAME);
	}

	public function testCachesIsEmpty() {
		self::assertNull(self::getObjectProperty('Setting', 'cache'));
		self::assertNull(self::getObjectProperty('Setting_Category', 'cache'));
	}

	public function testAppendCategory() {
		foreach ($this->names as $i => $name) {
			self::assertTrue(Setting_Category::append($name, $name . ' title', $name . ' description'), $name);
			$order = Nano::db()->getCell('select `order` from ' . Setting_Category::NAME . ' where name = ' . Nano::db()->quote($name));
			self::assertEquals($i, $order);
		}
	}

	/**
	 * @depends testAppendCategory
	 */
	public function testLoadingCache() {
		$this->testAppendCategory();
		self::assertType('Setting_Category', Setting_Category::get('category 01'));
		self::assertType('Setting_Category', Setting_Category::get('category 02'));
		self::assertType('Setting_Category', Setting_Category::get('category 03'));
		self::assertException(function() { Setting_Category::get(''); }, 'Nano_Exception', 'category "" not found');
		self::assertException(function() { Setting_Category::get('01'); }, 'Nano_Exception', 'category "01" not found');
	}

	/**
	 * @depends testAppendCategory
	 */
	public function testOrder() {
		$this->testAppendCategory();
		$cache = Setting_Category::all();
		$i = 0;
		foreach ($cache as $name => $category) {
			self::assertEquals($this->names[$i], $name);
			++$i;
		}
	}

	protected function tearDown() {
		$this->invalidateCaches();
		Nano::db()->delete(Setting_Category::NAME);
	}

	private function invalidateCaches() {
		self::setObjectProperty('Setting', 'cache', null);
		self::setObjectProperty('Setting_Category', 'cache', null);
	}

	private function addCategory($category, $name) {
	}

}