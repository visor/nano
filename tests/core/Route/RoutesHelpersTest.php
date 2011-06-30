<?php

/**
 * @group framework
 * @group routes
 */
class Core_Route_RoutesHelpersTest extends TestUtils_TestCase {

	/**
	 * @var Nano_Routes
	 */
	private $routes;

	protected function setUp() {
		$this->routes = new Nano_Routes();
	}

	public function testRoutesShouldHavePrefixesWhenItSetuped() {
		$this->routes->prefix('admin')
			->get('', 'admin', 'index')
			->get('/users', 'user', 'index')
		;

		$routes = self::getObjectProperty($this->routes, 'routes')->getArrayCopy();
		self::assertArrayHasKey('get', $routes);
		self::assertArrayHasKey('admin', $routes['get']->getArrayCopy());
		self::assertArrayHasKey('admin/users', $routes['get']->getArrayCopy());
	}

	public function testRoutesShouldHaveSuffixesWhenItSetuped() {
		$this->routes->suffix('.html')
			->get('users', 'user', 'index')
		;

		$routes = self::getObjectProperty($this->routes, 'routes')->getArrayCopy();
		self::assertArrayHasKey('get', $routes);
		self::assertArrayHasKey('users.html', $routes['get']->getArrayCopy());
	}

	public function testRoutesShouldBeSeparatedByRequestMethod() {
		$methods = array('get', 'post', 'head', 'put', 'delete');
		foreach ($methods as $index => $method) {
			$this->routes->add($method, (string)$index);
		}

		$routes = self::getObjectProperty($this->routes, 'routes')->getArrayCopy();
		foreach ($methods as $method) {
			self::assertArrayHasKey($method, $routes);
			self::assertEquals(1, count($routes[$method]));
		}
	}

	public function testModuleShouldPassedToEachCreatedRoute() {
		$this->routes
			->module('test-module')
				->get('test', 'index', 'index')
			->module('default')
				->get('', 'index', 'index')
		;

		$routes = self::getObjectProperty($this->routes, 'routes')->offsetGet('get')->getArrayCopy();
		self::assertArrayHasKey('', $routes);
		self::assertArrayHasKey('test', $routes);

		self::assertEquals('test-module', $routes['test']->module());
		self::assertEquals('default', $routes['']->module());
	}

	protected function tearDown() {
		$this->routes = null;
	}

}