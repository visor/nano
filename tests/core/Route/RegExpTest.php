<?php

/**
 * @group core
 * @group routes
 */
class Core_Route_RegExpTest extends TestUtils_TestCase {

	public function testShouldMatchIgnoreCase() {
		$route = new Nano_Route_RegExp('some-string-with-\d+', 'test', 'test', 'test');
		self::assertTrue($route->match('some-string-with-1'));
		self::assertTrue($route->match('Some-String-With-1'));

		$route = new Nano_Route_RegExp('Some-String-With-\d+', 'test', 'test', 'test');
		self::assertTrue($route->match('some-string-with-1'));
		self::assertTrue($route->match('Some-String-With-1'));
	}

	public function testMatchWholeStringOnly() {
		$route = new Nano_Route_RegExp('some-string', 'test', 'test', 'test');

		self::assertFalse($route->match('prefixes-some-string'));
		self::assertFalse($route->match('some-string-with-suffix'));
		self::assertTrue($route->match('some-string'));
	}

	public function testNamedParamsShouldBeSaved() {
		$route = new Nano_Route_RegExp('(some)\-(?P<param>string)', 'test', 'test', 'test');

		self::assertTrue($route->match('some-string'));

		$matches = $route->matches();
		self::assertArrayHasKey('param', $route->matches());
		self::assertEquals('string', $matches['param']);
	}

}