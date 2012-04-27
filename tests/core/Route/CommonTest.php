<?php

/**
 * @group core
 * @group routes
 */
class Core_Route_CommonTest extends TestUtils_TestCase {

	public function testTestEmptyUrl() {
		self::assertTrue(Nano_Route_Abstract::create('', 'index', 'index')->match(''));
		self::assertTrue(Nano_Route_Abstract::create(null, 'index', 'index')->match(''));
	}

	public function testTestParametersParsing() {
		$route = Nano_Route_Abstract::create('~show\/(?P<page>[-\w]+)', 'index', 'index');

		self::assertFalse($route->match('show/some-page!'));
		self::assertTrue($route->match('show/some-page'));

		$params = $route->matches();

		self::assertArrayHasKey('page', $params);
		self::assertEquals('some-page', $params['page']);
	}

	public function testParamsShouldPassedIntoRoute() {
		$params = Nano_Route_Abstract::create('', 'index', 'index', null, array('param' => 'value'))->params();
		self::assertArrayHasKey('param', $params);
		self::assertEquals('value', $params['param']);
	}

}