<?php

/**
 * @group core
 * @group routes
 */
class Core_Route_RegExpTest extends \Nano\TestUtils\TestCase {

	public function testShouldMatchIgnoreCase() {
		$route = new Nano\Route\RegExp('some-string-with-\d+', 'test', 'test', 'test');
		self::assertTrue($route->match('some-string-with-1'));
		self::assertTrue($route->match('Some-String-With-1'));

		$route = new Nano\Route\RegExp('Some-String-With-\d+', 'test', 'test', 'test');
		self::assertTrue($route->match('some-string-with-1'));
		self::assertTrue($route->match('Some-String-With-1'));
	}

	public function testMatchWholeStringOnly() {
		$route = new Nano\Route\RegExp('some-string', 'test', 'test', 'test');

		self::assertFalse($route->match('prefixes-some-string'));
		self::assertFalse($route->match('some-string-with-suffix'));
		self::assertTrue($route->match('some-string'));
	}

	public function testNamedParamsShouldBeSaved() {
		$route = new Nano\Route\RegExp('(some)\-(?P<param>string)', 'test', 'test', 'test');

		self::assertTrue($route->match('some-string'));

		$matches = $route->params();
		self::assertArrayHasKey('param', $route->params());
		self::assertEquals('string', $matches['param']);
	}

}