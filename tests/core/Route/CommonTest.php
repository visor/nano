<?php

/**
 * @group framework
 * @group routes
 */
class Core_Route_CommonTest extends TestUtils_TestCase {

	public function testTestEmptyUrl() {
		self::assertTrue(Nano_Route::create('', 'index', 'index')->match(''));
		self::assertTrue(Nano_Route::create(null, 'index', 'index')->match(''));
	}

	public function testTestParametersParsing() {
		$route = Nano_Route::create('~show\/(?P<page>[-\w]+)', 'index', 'index');

		self::assertFalse($route->match('show/some-page!'));
		self::assertTrue($route->match('show/some-page'));

		$params = $route->matches();

		self::assertArrayHasKey('page', $params);
		self::assertEquals('some-page', $params['page']);
	}

}