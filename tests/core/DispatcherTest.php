<?php

/**
 * @group framework
 * @group routes
 * @group dispatcher
 */
class DispatcherTest extends TestUtils_TestCase {

	/**
	 * @var boolean
	 */
	protected $backupGlobals = true;

	/**
	 * @var Nano_Dispatcher
	 */
	private $dispatcher;

	protected function setUp() {
		$this->dispatcher = new Nano_Dispatcher();
	}

	public function testFormatting() {
		$this->assertEquals('TestController',         Nano_Dispatcher::formatName('test', true));
		$this->assertEquals('AnotherTestController',  Nano_Dispatcher::formatName('another-test', true));
		$this->assertEquals('Another_testController', Nano_Dispatcher::formatName('another_test', true));

		$this->assertEquals('testAction',             Nano_Dispatcher::formatName('test', false));
		$this->assertEquals('anotherTestAction',      Nano_Dispatcher::formatName('another-test', false));
		$this->assertEquals('another_testAction',     Nano_Dispatcher::formatName('another_test', false));
	}

	public function testRouteFindingForEmptyUrl() {
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$routes = new Nano_Routes();
		$routes->add('get', '', 'index', 'index');

		$urls = array('', '/', '//');
		foreach ($urls as $url) {
			$route = $this->dispatcher->getRoute($routes, $url);
			self::assertInstanceOf('Nano_Route', $route, 'for url: [' . $url . ']');
			$this->assertEquals('index::index() when location matches []', $route->__toString());
		}
	}

	public function testGetController() {
		$c = $this->dispatcher->getController(Nano_Route::create('', 'test', 'test'));
		self::assertInstanceOf('Nano_C', $c);
		self::assertInstanceOf('TestController', $c);
	}

	public function testDetectingContextBySuffix() {
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$routes = new Nano_Routes();
		$routes
			->suffix('~(\.(?P<context>xml|rss))?')
				->get('index', 'test', 'index')
		;

		$this->dispatcher->run($this->dispatcher->getRoute($routes, 'index.xml'));
		self::assertEquals('xml', $this->dispatcher->controllerInstance()->context);

		$this->dispatcher->run($this->dispatcher->getRoute($routes, 'index.rss'));
		self::assertEquals('rss', $this->dispatcher->controllerInstance()->context);
	}

}