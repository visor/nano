<?php

/**
 * @group core
 * @group routes
 */
class Core_Route_CommonTest extends \Nano\TestUtils\TestCase {

	public function testControllerShouldReturnPassedParam() {
		self::assertEquals('some', \Nano\Route\Common::create('', 'some', 'action')->controller());
	}

	public function testControllerClassShouldReturnFormatterControllerClassName() {
		self::assertEquals('App\Controller\Some', \Nano\Route\Common::create('', 'some', 'action')->controllerClass());
		self::assertEquals('Module\Common\Controller\Some', \Nano\Route\Common::create('', 'some', 'action', 'common')->controllerClass());
	}

	public function testStringCasting() {
		self::assertEquals('some::action() when location matches [home]', \Nano\Route\Common::create('home', 'some', 'action')->__toString());
	}

	public function testTestEmptyUrl() {
		self::assertTrue(\Nano\Route\Common::create('', 'index', 'index')->match(''));
		self::assertTrue(\Nano\Route\Common::create(null, 'index', 'index')->match(''));
	}

	public function testTestParametersParsing() {
		$route = \Nano\Route\Common::create('~show\/(?P<page>[-\w]+)', 'index', 'index');

		self::assertFalse($route->match('show/some-page!'));
		self::assertTrue($route->match('show/some-page'));

		$params = $route->matches();

		self::assertArrayHasKey('page', $params);
		self::assertEquals('some-page', $params['page']);
	}

	public function testParamsShouldPassedIntoRoute() {
		$params = \Nano\Route\Common::create('', 'index', 'index', null, array('param' => 'value'))->params();
		self::assertArrayHasKey('param', $params);
		self::assertEquals('value', $params['param']);
	}

}