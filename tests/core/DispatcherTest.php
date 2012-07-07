<?php

/**
 * @group core
 */
class Core_DispatcherTest extends \Nano\TestUtils\TestCase {

	/**
	 * @var boolean
	 */
	protected $backupGlobals = true;

	/**
	 * @var \Nano\Application\Dispatcher
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
		$this->assertEquals('App\Controller\Test',         \Nano\Application\Dispatcher::formatName('test', true));
		$this->assertEquals('App\Controller\AnotherTest',  \Nano\Application\Dispatcher::formatName('another-test', true));
		$this->assertEquals('App\Controller\Another_test', \Nano\Application\Dispatcher::formatName('another_test', true));

		$this->assertEquals('Module\Test\Controller\Test', \Nano\Application\Dispatcher::formatName('test', true, 'test'));
	}

	public function testActionsNamesFormatting() {
		$this->assertEquals('testAction',             \Nano\Application\Dispatcher::formatName('test', false));
		$this->assertEquals('anotherTestAction',      \Nano\Application\Dispatcher::formatName('another-test', false));
		$this->assertEquals('another_testAction',     \Nano\Application\Dispatcher::formatName('another_test', false));
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
		self::assertInstanceOf('\Nano\Controller', $c);
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
		$application->dispatcher->setResponse(new \Nano\Controller\Response\Test($application));

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
		self::assertInstanceOf('\Nano\Controller\Response', $this->dispatcher->getResponse());
	}

	protected function tearDown() {
		unSet($this->dispatcher);
		$this->app->restore();
	}

}