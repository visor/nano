<?php

/**
 * @group core
 */
class Core_DispatcherTest extends TestUtils_TestCase {

	/**
	 * @var boolean
	 */
	protected $backupGlobals = true;

	/**
	 * @var Nano_Dispatcher
	 */
	private $dispatcher;

	protected function setUp() {
		$application      = new Application();
		$this->dispatcher = new Nano_Dispatcher($application);
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
		$this->dispatcher->application()->withRootDir($this->files->get($this, ''));
		$c = $this->dispatcher->getController(Nano_Route::create('', 'test', 'test'));
		self::assertInstanceOf('Nano_C', $c);
		self::assertInstanceOf('TestController', $c);
	}

	public function testDetectingContextBySuffix() {
		$this->dispatcher->application()->withRootDir($this->files->get($this, ''));
		$this->dispatcher->setResponse(new Nano_C_Response_Test($this->dispatcher->application()));

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

	public function testShouldReturnStatusCodeWhenNotFound() {
		$this->dispatcher
			->throwExceptions(true)
			->setResponse(new Nano_C_Response_Test($this->dispatcher->application()))
		;
		$routes = new Nano_Routes();
		$routes->get('', 'response-test', 'not-found');

		$this->dispatcher->dispatch($routes, '');
		self::assertTrue($this->dispatcher->getResponse()->isModified());
		self::assertEquals(404, $this->dispatcher->getResponse()->getStatus());
	}

	public function testShouldReturnStatusCodeWhenInternalError() {
		$this->dispatcher
			->throwExceptions(true)
			->setResponse(new Nano_C_Response_Test($this->dispatcher->application()))
		;
		$routes = new Nano_Routes();
		$routes->get('', 'response-test', 'error');

		$this->dispatcher->dispatch($routes, '');
		self::assertTrue($this->dispatcher->getResponse()->isModified());
		self::assertEquals(500, $this->dispatcher->getResponse()->getStatus());
	}

	protected function tearDown() {
		unSet($this->dispatcher);
	}

}