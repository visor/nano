<?php

/**
 * @group core
 * @group framework
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
		$this->setExpectedException('Nano_Exception_NotFound', 'Custom dispatcher fails');

		$this->dispatcher
			->throwExceptions(true)
			->dispatch(new Nano_Routes(), '')
		;
	}

	protected function tearDown() {
		unset($_COOKIE['accept']);
	}

}
