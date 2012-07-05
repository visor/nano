<?php

/**
 * @group core
 */
class Core_NanoTest extends TestUtils_TestCase {

	protected $backup;

	protected function setUp() {
		$this->backup = self::getObjectProperty('Nano', 'app');
		self::setObjectProperty('Nano', 'app', null);
		unSet($_COOKIE['PHPUNIT_SELENIUM_TEST_ID']);
	}

	public function testSetApplicationShouldThrowExceptionWhenAlreadySetted() {
		$this->setExpectedException('\Nano\Exception', 'Application inctance already created');
		Nano::setApplication($GLOBALS['application']);
		Nano::setApplication($GLOBALS['application']);
	}

	public function testSetApplicationShouldStoreValue() {
		Nano::setApplication($GLOBALS['application']);
		self::assertSame($GLOBALS['application'], self::getObjectProperty('Nano', 'app'));
	}

	public function testSetApplicationShouldResetValueWhenNullPassed() {
		Nano::setApplication(null);
		self::assertNull(null, self::getObjectProperty('Nano', 'app'));
	}

	public function testAppShouldReturnStoredValue() {
		self::assertNull(Nano::app());
		Nano::setApplication($GLOBALS['application']);
		self::assertSame($GLOBALS['application'], Nano::app());
	}

	public function testIsTestingShouldReturnTrueWhenCookieExists() {
		$_COOKIE['PHPUNIT_SELENIUM_TEST_ID'] = 'test';
		self::assertTrue(Nano::isTesting());
	}

	public function testIsTestingShouldReturnTrueWhenConstantDefined() {
		if (defined('TESTING')) {
			self::assertTrue(Nano::isTesting());
		}
	}

	protected function tearDown() {
		unSet($_COOKIE['PHPUNIT_SELENIUM_TEST_ID']);
		self::setObjectProperty('Nano', 'app', $this->backup);
	}

}