<?php

/**
 * @group core
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

	public function testRegexpLocationWithSuffix() {
		$key  = '~show/(?P<page>[-\w]+)';
		$data = array(
			  '.html'                      => '/^show\/(?P<page>[-\w]+)\.html$/'
			, '~(?P<context>\.(xml|rss))?' => '/^show\/(?P<page>[-\w]+)(?P<context>\.(xml|rss))?$/'
		);
		foreach ($data as $suffix => $location) {
			$this->routes->suffix($suffix)->get($key, 'index', 'index');
			$routes = self::getObjectProperty($this->routes, 'routes')->getArrayCopy();
			self::assertArrayHasKey($location, $routes['get']->getArrayCopy());
			self::assertEquals($location, $routes['get']->offsetGet($location)->location());
		}
	}

	public function testRegexpLocationWithPrefix() {
		$key  = '~show/(?P<page>[-\w]+)';
		$data = array(
			  'admin/'              => '/^admin\/show\/(?P<page>[-\w]+)$/'
			, '~(?P<lang>(ru|en))/' => '/^(?P<lang>(ru|en))\/show\/(?P<page>[-\w]+)$/'
		);
		foreach ($data as $prefix => $location) {
			$this->routes->prefix($prefix)->get($key, 'index', 'index');
			$routes = self::getObjectProperty($this->routes, 'routes')->getArrayCopy();
			self::assertArrayHasKey($location, $routes['get']->getArrayCopy());
			self::assertEquals($location, $routes['get']->offsetGet($location)->location());
		}
	}

	public function testNoRegexInLocationParts() {
		$this->routes->prefix('admin')->suffix('.html')->get('', 'index', 'index');
		$this->routes->prefix('admin')->suffix('.html')->get('/index', 'index', 'index');

		$keys   = array('admin.html', 'admin/index.html');
		$routes = self::getObjectProperty($this->routes, 'routes')->getArrayCopy();
		foreach ($keys as $location) {
			self::assertArrayHasKey($location, $routes['get']->getArrayCopy());
			self::assertEquals($location, $routes['get']->offsetGet($location)->location());
		}
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