<?php

class Settings_SettingsTest extends TestUtils_TestCase {

	protected function setUp() {
		$this->invalidateCaches();
		Nano::db()->delete(Setting_Category::NAME);
		Setting_Category::append('some', 'some category');
	}

	public function testCachesIsEmpty() {
		self::assertNull(self::getObjectProperty('Setting', 'cache'));
		self::assertNull(self::getObjectProperty('Setting_Category', 'cache'));
	}

	public function testOnlyOneFromDb() {
		$mock = $this->getMock('Setting', array('load'), array(null, true), 'Setting_Mock1');
		$mock
			->staticExpects($this->exactly(2))
			->method('load')
		;
		Setting_Mock1::get('some', 'some');
		Setting_Mock1::get('some', 'some');

		$mock = $this->getMock('Setting', array('loadCache'), array(null, true), 'Setting_Mock2');
		$mock
			->staticExpects($this->once())
			->method('loadCache')
			->will($this->returnValue(array()));
		;
		Setting_Mock2::get('some', 'some');
		Setting_Mock2::get('some', 'some');
	}

	public function testAppend() {
		self::assertTrue(Setting::append('some', 'text', 's1', 's1'));
		self::assertTrue(Setting::append('some', 'text', 's2', 's2', null, 1));
		self::assertFalse(Setting::append('other', 'text', 's2', 's2'));
	}

	/**
	 * @depends testAppend
	 */
	public function testGet() {
		$this->testAppend();
		self::assertEquals(null, Setting::get('some', 's1'));
		self::assertEquals(1, Setting::get('some', 's2'));
	}

	protected function tearDown() {
		$this->invalidateCaches();
		Nano::db()->delete(Setting_Category::NAME);
	}

	private function invalidateCaches() {
		self::setObjectProperty('Setting', 'cache', null);
		self::setObjectProperty('Setting_Category', 'cache', null);
	}

	private function addSetting($category, $name, $value) {
	}

}