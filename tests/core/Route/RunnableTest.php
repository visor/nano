<?php

/**
 * @group core
 * @group routes
 */
class Core_Route_RunnableTest extends TestUtils_TestCase {

	/**
	 * @var \Nano\Application
	 */
	protected $application;

	protected function setUp() {
		$this->app->backup();
		include_once $this->files->get($this, '/TestRunnableRoute.php');

		$this->application = new \Nano\Application();
		$this->application
			->withConfigurationFormat('php')
			->withRootDir($GLOBALS['application']->rootDir)
			->configure()
		;

		$this->application->dispatcher->setResponse(new \Nano\Controller\Response\Test($this->application));
	}

	public function testDispatcherShouldRunRouteWithoutInvocingController() {
		$routes = new \Nano\Routes();
		$route  = new TestRunnableRouteAbstract();
		$routes->addRoute('get', $route);

		$_SERVER['REQUEST_METHOD'] = 'get';
		self::assertFalse($route->wasRun());
		self::assertNull($this->application->dispatcher->dispatch($routes, TestRunnableRouteAbstract::LOCATION));
		self::assertTrue($route->wasRun());
	}

	protected function tearDown() {
		unSet($this->application);
		$this->app->restore();
	}

}