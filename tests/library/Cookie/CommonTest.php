<?php

/**
 * @group cookie
 */
class Library_Cookie_CommonTest extends \Nano\TestUtils\TestCase {

	/**
	 * @var \Nano\Util\Cookie
	 */
	protected $cookie;

	protected function setUp() {
		$this->cookie = new \Nano\Util\Cookie('example.com');
		$_COOKIE = array();
	}

	public function testGetShouldReturnValueFromCookieArray() {
		$_COOKIE['example'] = 'value';
		self::assertEquals($_COOKIE['example'], $this->cookie->get('example'));
	}

	public function testGetShouldReturnDefaultValueIfNotExists() {
		self::assertEquals('default', $this->cookie->get('example', 'default'));
	}

	public function testCookieArrayShouldContainValueAfterSet() {
		$this->cookie->set('name', 'value', 30);
		self::assertArrayHasKey('name', $_COOKIE);
		self::assertEquals('value', $_COOKIE['name']);
	}

	public function testCookieArrayShouldNotContainValueAfterReset() {
		$_COOKIE['example'] = 'value';

		$this->cookie->erase('example');
		self::assertArrayNotHasKey('example', $_COOKIE);
	}

	protected function tearDown() {
		$_COOKIE = array();
		unSet($this->cookie);
	}

}