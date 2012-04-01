<?php

/**
 * @group core
 */
class Core_CustomDispatcherTest extends TestUtils_TestCase {

	/**
	 * @var Nano_Dispatcher
	 */
	private $dispatcher;

	protected function setUp() {
		require_once $this->files->get($this, '/Test_Dispatcher.php');

		$application = new Application();
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
		self::assertEquals('dispatched', $this->dispatcher->dispatch(new Nano_Routes(), ''));
	}

	public function testNotAcceptCustom() {
		$this->setExpectedException('Nano_Exception_NotFound', 'Custom dispatcher fails');

		$response = new Nano_C_Response_Test($this->dispatcher->application());
		$this->dispatcher
			->setResponse($response)
			->throwExceptions(true)
			->dispatch(new Nano_Routes(), '')
		;
	}

	protected function tearDown() {
		unset($_COOKIE['accept']);
	}

}
