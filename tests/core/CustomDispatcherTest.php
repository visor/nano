<?php

/**
 * @group nano
 */
class CustomDispatcherTest extends TestUtils_TestCase {

	/**
	 * @var Nano_Dispatcher
	 */
	private $dispatcher;

	protected function setUp() {
		require_once $this->getTestFile('/Test_Dispatcher.php');
		$this->dispatcher = new Nano_Dispatcher();
		$this->dispatcher->setCustom(new Test_Dispatcher());
		$this->dispatcher->throwExceptions(true);
	}

	public function testAcceptCustom() {
		$_COOKIE['accept'] = true;
		self::assertEquals('dispatched', $this->dispatcher->dispatch(new Nano_Routes(), ''));
	}

	public function testNotAcceptCustom() {
		$dispatcher = $this->dispatcher;
		self::assertException(function() use ($dispatcher) { $dispatcher->throwExceptions(true)->dispatch(new Nano_Routes(), ''); }, 'Exception', '404');
	}

	protected function tearDown() {
		unset($_COOKIE['accept']);
	}

}
