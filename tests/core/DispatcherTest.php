<?php

class DispatcherTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Nano_Dispatcher
	 */
	private $dispatcher;

	protected function setUp() {
		$this->dispatcher = new Nano_Dispatcher();
	}

	public function testFormatting() {
		$this->assertEquals('TestController',        Nano_Dispatcher::formatName('test', true));
		$this->assertEquals('AnotherTestController', Nano_Dispatcher::formatName('another-test', true));
		$this->assertEquals('Another_testController', Nano_Dispatcher::formatName('another_test', true));

		$this->assertEquals('testAction',        Nano_Dispatcher::formatName('test', false));
		$this->assertEquals('anotherTestAction', Nano_Dispatcher::formatName('another-test', false));
		$this->assertEquals('another_testAction', Nano_Dispatcher::formatName('another_test', false));
	}

	public function testTestEmptyUrl() {
		$this->assertTrue($this->dispatcher->test(Nano_Route::create('', 'index', 'index'), ''));
	}

	public function testTestUrlWithParameters() {
		$route = Nano_Route::create('show/(?P<page>[-\w]+)', 'index', 'index');
		$this->assertFalse($this->dispatcher->test($route, 'show/some-page!'));
		$this->assertTrue($this->dispatcher->test($route, 'show/some-page'));

		$this->assertArrayHasKey('page', $this->dispatcher->params());
		$this->assertEquals('some-page', $this->dispatcher->param('page', null));
	}

	public function testRouteFindingForEmptyUrl() {
		$routes = new Nano_Routes();
		$routes->add('', 'index', 'index');

		$route1 = $this->dispatcher->getRoute($routes, '/');
		$route2 = $this->dispatcher->getRoute($routes, '');

		$this->assertType('Nano_Route', $route1);
		$this->assertType('Nano_Route', $route2);
		$this->assertEquals('index::index() when /^$/', $route1->__toString());
		$this->assertEquals('index::index() when /^$/', $route2->__toString());
	}

	public function testGetController() {
		$c = $this->dispatcher->getController(Nano_Route::create('', 'test', 'test'));
		$this->assertType('Nano_C', $c);
		$this->assertType('TestController', $c);
	}

	protected function tearDown() {
		unset($this->dispatcher);
	}

}