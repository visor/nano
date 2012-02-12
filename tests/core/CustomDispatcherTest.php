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

		$this->dispatcher = new Nano_Dispatcher(new Application());
		$this->dispatcher->setCustom(new Test_Dispatcher());
		$this->dispatcher->throwExceptions(true);
	}

	public function testAcceptCustom() {
		$_COOKIE['accept'] = true;
		self::assertEquals('dispatched', $this->dispatcher->dispatch(new Nano_Routes(), ''));
	}

	public function testNotAcceptCustom() {
		$response = new Nano_C_Response_Test($this->dispatcher->application());
		$this->dispatcher
			->setResponse($response)
			->throwExceptions(true)
			->dispatch(new Nano_Routes(), '')
		;

		self::assertInternalType('string', $response->getBody());
		self::assertContains('Nano_Exception_NotFound', $response->getBody());
		self::assertContains('Custom dispatcher fails', $response->getBody());
	}

	protected function tearDown() {
		unset($_COOKIE['accept']);
	}

}
