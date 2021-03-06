<?php

/**
 * @group core
 */
class Core_CustomDispatcherTest extends \Nano\TestUtils\TestCase {

	/**
	 * @var \Nano\Application\Dispatcher
	 */
	private $dispatcher;

	protected function setUp() {
		$this->app->backup();

		require_once $this->files->get($this, '/Test_Dispatcher.php');

		$application = new \Nano\Application();
		$application
			->withConfigurationFormat('php')
			->withRootDir(__DIR__)
			->configure()
		;
		$this->dispatcher = $application->dispatcher;
		$this->dispatcher->setCustom(new Test_Dispatcher());
		$this->dispatcher->throwExceptions(true);
	}

	public function testAcceptCustom() {
		$_COOKIE['accept'] = true;
		self::assertEquals('dispatched', $this->dispatcher->dispatch(new \Nano\Routes(), ''));
	}

	public function testNotAcceptCustom() {
		$this->setExpectedException('\Nano\Exception\NotFound', 'Custom dispatcher fails');

		$response = new \Nano\Controller\Response\Test();
		$this->dispatcher
			->setResponse($response)
			->throwExceptions(true)
			->dispatch(new \Nano\Routes(), '')
		;
	}

	protected function tearDown() {
		unset($_COOKIE['accept']);
		$this->app->restore();
	}

}
