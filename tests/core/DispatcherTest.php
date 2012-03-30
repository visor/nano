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
		$application = new Application();
		$application
			->withConfigurationFormat('php')
			->withRootDir($GLOBALS['application']->rootDir)
			->configure()
		;
		$this->dispatcher = new Nano_Dispatcher($application);
	}

	public function testControllersNamesFormatting() {
		$this->assertEquals('TestController',         Nano_Dispatcher::formatName('test', true));
		$this->assertEquals('AnotherTestController',  Nano_Dispatcher::formatName('another-test', true));
		$this->assertEquals('Another_testController', Nano_Dispatcher::formatName('another_test', true));

		$this->assertEquals('Test_Module\\TestController', Nano_Dispatcher::formatName('test', true, 'Test_Module'));
	}

	public function testActionsNamesFormatting() {
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

	public function testTestingRouteShouldReturnFalseWhenNotMatches() {
		$route = new Nano_Route_Static('some-string', 'test', 'test', 'test');
		self::assertFalse($this->dispatcher->test($route, 'other-string'));
	}

	public function testGetController() {
		$application = new Application();
		$application
			->withConfigurationFormat('php')
			->withRootDir($this->files->get($this, ''))
			->configure()
		;

		$c = $application->dispatcher->getController(Nano_Route::create('', 'test', 'test'));
		self::assertInstanceOf('Nano_C', $c);
		self::assertInstanceOf('TestController', $c);
	}

	public function testGetControllerShouldThrowWhenClassNotExists() {
		$this->setExpectedException('Nano_Exception_NotFound', 'Controller class not found');
		$this->dispatcher
			->setResponse(new Nano_C_Response_Test($this->dispatcher->application()))
			->getController(new Nano_Route_Static('test', 'std-class', 'index', null))
		;
	}

	public function testGetControllerShouldThrowWhenNotControllerClassRequired() {
		$this->setExpectedException('Nano_Exception_InternalError', 'Not a controller class: NotController');

		$application = new Application();
		$application
			->withConfigurationFormat('php')
			->withRootDir($this->files->get($this, ''))
			->configure()
		;

		$application->dispatcher->setResponse(new Nano_C_Response_Test($application));
		$application->dispatcher->getController(Nano_Route::create('', 'not', 'test'));
	}

	public function testGetControllerShouldThrowWhenAbstractClassRequired() {
		$this->setExpectedException('Nano_Exception_InternalError', 'Not a controller class: AbstractController');

		$application = new Application();
		$application
			->withConfigurationFormat('php')
			->withRootDir($this->files->get($this, ''))
			->configure()
		;

		$application->dispatcher->setResponse(new Nano_C_Response_Test($application));
		$application->dispatcher->getController(Nano_Route::create('', 'abstract', 'test'));
	}

	public function testDetectingContextBySuffix() {
		$application = new Application();
		$application
			->withConfigurationFormat('php')
			->withRootDir($this->files->get($this, ''))
			->configure()
		;
		$application->dispatcher->setResponse(new Nano_C_Response_Test($application));

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$routes = new Nano_Routes();
		$routes
			->suffix('~(\.(?P<context>xml|rss))?')
				->get('index', 'test', 'index')
		;

		$application->dispatcher->run($application->dispatcher->getRoute($routes, 'index.xml'));
		self::assertEquals('xml', $application->dispatcher->controllerInstance()->context);

		$application->dispatcher->run($application->dispatcher->getRoute($routes, 'index.rss'));
		self::assertEquals('rss', $application->dispatcher->controllerInstance()->context);
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

	public function testDispatchShouldSendNotFoundExceptionWhenNoRoutesMatched() {
		$routes = new Nano_Routes();
		$routes->post('other', 'test', 'test');

		$response = new Nano_C_Response_Test($this->dispatcher->application());
		$this->dispatcher
			->throwExceptions(true)
			->setResponse($response)
			->dispatch($routes, 'some')
		;

		self::assertContains('Nano_Exception_NotFound', $response->getBody());
		self::assertContains('Route not found', $response->getBody());
	}

	public function testSettingParamsShouldSetupModuleControllerActionParams() {
		$this->dispatcher->setParams(array(
			'module'       => 'default'
			, 'controller' => 'public'
			, 'action'     => 'index'
		));
		self::assertEquals('default', $this->dispatcher->module());
		self::assertEquals('public', $this->dispatcher->controller());
		self::assertEquals('index', $this->dispatcher->action());
		self::assertEquals(array(), $this->dispatcher->params());
	}

	public function testGetResponseShouldCreateInstanceWhenNull() {
		self::setObjectProperty($this->dispatcher, 'response', null);
		self::assertInstanceOf('Nano_C_Response', $this->dispatcher->getResponse());
	}

	protected function tearDown() {
		unSet($this->dispatcher);
	}

}