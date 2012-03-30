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

		$this->assertLocationExists('get', 'admin');
		$this->assertLocationExists('get', 'admin/users');
	}

	public function testRoutesShouldHaveSuffixesWhenItSetuped() {
		$this->routes->suffix('.html')
			->get('users', 'user', 'index')
		;

		$this->assertLocationExists('get', 'users.html');
	}

	public function testRegexpLocationWithSuffix() {
		$key  = '~show/(?P<page>[-\w]+)';
		$data = array(
			  '.html'                      => '/^show\/(?P<page>[-\w]+)\.html$/i'
			, '~(?P<context>\.(xml|rss))?' => '/^show\/(?P<page>[-\w]+)(?P<context>\.(xml|rss))?$/i'
		);
		foreach ($data as $suffix => $location) {
			$this->routes->suffix($suffix)->get($key, 'index', 'index');
			$this->assertLocationExists('get', $location);
		}
	}

	public function testRegexpLocationWithPrefix() {
		$key  = '~show/(?P<page>[-\w]+)';
		$data = array(
			  'admin/'              => '/^admin\/show\/(?P<page>[-\w]+)$/i'
			, '~(?P<lang>(ru|en))/' => '/^(?P<lang>(ru|en))\/show\/(?P<page>[-\w]+)$/i'
		);
		foreach ($data as $prefix => $location) {
			$this->routes->prefix($prefix)->get($key, 'index', 'index');
			$this->assertLocationExists('get', $location);
		}
	}

	public function testNoRegexInLocationParts() {
		$this->routes->prefix('admin')->suffix('.html')->get('', 'index', 'index');
		$this->routes->prefix('admin')->suffix('.html')->get('/index', 'index', 'index');

		$keys = array('admin.html', 'admin/index.html');
		foreach ($keys as $location) {
			$this->assertLocationExists('get', $location);
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

		$this->assertLocationExists('get', 'test');
		$this->assertLocationExists('get', '');
		$routes = self::getObjectProperty($this->routes, 'routes')->offsetGet('get')->getArrayCopy();
		self::assertEquals('test-module', $routes[0]->module());
		self::assertEquals('default', $routes[1]->module());
	}

	protected function assertLocationExists($method, $location) {
		self::assertArrayHasKey($method, $this->routes->getIterator()->getArrayCopy());
		$routesArray = $this->routes->getRoutes($method)->getArrayCopy();

		foreach ($routesArray as /** @var Nano_Route $route */ $route) {
			if ($route->location() == $location) {
				return;
			}
		}
		self::fail('Failed assertion: Routes contains ' . $method . ': ' . $location);
	}

	protected function tearDown() {
		$this->routes = null;
	}

}