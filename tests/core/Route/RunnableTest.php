<?php

/**
 * @group core
 * @group routes
 */
class Core_Route_RunnableTest extends TestUtils_TestCase {

	/**
	 * @var Application
	 */
	protected $application;

	protected function setUp() {
		$this->app->backup();
		include_once $this->files->get($this, '/TestRunnableRoute.php');

		$this->application = new Application();
		$this->application
			->withConfigurationFormat('php')
			->withRootDir($GLOBALS['application']->rootDir)
			->configure()
		;

		$this->application->dispatcher->setResponse(new Nano_C_Response_Test($this->application));
	}

	public function testDispatcherShouldRunRouteWithoutInvocingController() {
		$routes = new Nano_Routes();
		$route  = new TestRunnableRoute();
		$routes->addRoute('get', $route);

		$_SERVER['REQUEST_METHOD'] = 'get';
		self::assertFalse($route->wasRun());
		self::assertNull($this->application->dispatcher->dispatch($routes, TestRunnableRoute::LOCATION));
		self::assertTrue($route->wasRun());
	}

	protected function tearDown() {
		unSet($this->application);
		$this->app->restore();
	}

}