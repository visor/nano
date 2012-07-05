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
	 * @var \Nano\Dispatcher
	 */
	private $dispatcher;

	protected function setUp() {
		$this->app->backup();

		$application = new \Nano\Application();
		$application
			->withConfigurationFormat('php')
			->withRootDir($GLOBALS['application']->rootDir)
			->configure()
		;
		$this->dispatcher = $application->dispatcher;
	}

	public function testControllersNamesFormatting() {
		$this->assertEquals('TestController',         \Nano\Dispatcher::formatName('test', true));
		$this->assertEquals('AnotherTestController',  \Nano\Dispatcher::formatName('another-test', true));
		$this->assertEquals('Another_testController', \Nano\Dispatcher::formatName('another_test', true));

		$this->assertEquals('Test_Module\\TestController', \Nano\Dispatcher::formatName('test', true, 'Test_Module'));
	}

	public function testActionsNamesFormatting() {
		$this->assertEquals('testAction',             \Nano\Dispatcher::formatName('test', false));
		$this->assertEquals('anotherTestAction',      \Nano\Dispatcher::formatName('another-test', false));
		$this->assertEquals('another_testAction',     \Nano\Dispatcher::formatName('another_test', false));
	}

	public function testRouteFindingForEmptyUrl() {
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$routes = new \Nano\Routes();
		$routes->add('get', '', 'index', 'index');

		$urls = array('', '/', '//');
		foreach ($urls as $url) {
			$route = $this->dispatcher->getRoute($routes, $url);
			self::assertInstanceOf('\Nano\Route\Common', $route, 'for url: [' . $url . ']');
			$this->assertEquals('index::index() when location matches []', $route->__toString());
		}
	}

	public function testGetController() {
		Nano::setApplication(null);
		$application = new \Nano\Application();
		$application
			->withConfigurationFormat('php')
			->withRootDir($this->files->get($this, ''))
			->configure()
		;

		$c = $application->dispatcher->getController(\Nano\Route\Common::create('', 'test', 'test'));
		self::assertInstanceOf('Nano_C', $c);
		self::assertInstanceOf('App\Controller\Test', $c);
	}

	public function testDetectingContextBySuffix() {
		Nano::setApplication(null);
		$application = new \Nano\Application();
		$application
			->withConfigurationFormat('php')
			->withRootDir($this->files->get($this, ''))
			->configure()
		;
		$application->dispatcher->setResponse(new Nano_C_Response_Test($application));

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$routes = new \Nano\Routes();
		$routes
			->suffix('~(\.(?P<context>xml|rss))?')
				->get('index', 'test', 'index')
		;

		self::assertInstanceOf('Nano\Route\RegExp', $application->dispatcher->getRoute($routes, 'index.xml'));
		$application->dispatcher->run($application->dispatcher->getRoute($routes, 'index.xml'));
		self::assertEquals('xml', $application->dispatcher->controllerInstance()->context);
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
		$this->app->restore();
	}

}