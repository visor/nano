<?php

/**
 * @group core
 * @group routes
 */
class Core_Route_StaticTest extends TestUtils_TestCase {

	public function testShouldMatchIgnoreCase() {
		$route = new Nano_Route_Static('some-string', 'test', 'test', 'test');
		self::assertTrue($route->match('Some-String'));
		self::assertTrue($route->match('some-string'));

		$route = new Nano_Route_Static('Some-String', 'test', 'test', 'test');
		self::assertTrue($route->match('Some-String'));
		self::assertTrue($route->match('some-string'));
	}

	public function testMatchWholeStringOnly() {
		$route = new Nano_Route_Static('some-string', 'test', 'test', 'test');

		self::assertFalse($route->match('prefixed-some-string'));
		self::assertFalse($route->match('some-string-with-suffix'));
	}

}