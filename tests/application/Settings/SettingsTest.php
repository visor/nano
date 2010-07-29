<?php

class Settings_SettingsTest extends TestUtils_TestCase {

	protected function setUp() {
		$this->invalidateCaches();
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

	protected function tearDown() {
		$this->invalidateCaches();
	}

	private function invalidateCaches() {
		self::setObjectProperty('Setting', 'cache', null);
		self::setObjectProperty('Setting_Category', 'cache', null);
	}
}