<?php

/**
 * @group core
 */
class Core_NanoTest extends TestUtils_TestCase {

	protected function setUp() {
		unSet($_COOKIE['PHPUNIT_SELENIUM_TEST_ID']);
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
	}

}