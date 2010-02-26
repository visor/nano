<?php

class CustomDispatcherTest extends TestUtils_TestCase {

	/**
	 * @var Nano_Dispatcher
	 */
	private $dispatcher;

	public static function setUpBeforeClass() {
		require_once dirName(__FILE__) . DS . '_files' . DS . 'Test_Dispatcher.php';
	}

	protected function setUp() {
		$this->dispatcher = new Nano_Dispatcher();
		$this->dispatcher->setCustom(new Test_Dispatcher());
	}

	public function testAcceptCustom() {
		$_COOKIE['accept'] = true;
		self::assertEquals('dispatched', $this->dispatcher->dispatch(new Nano_Routes(), ''));
	}

	public function testNotAcceptCustom() {
		$this->setExpectedException('Exception', '404');
		$this->dispatcher->dispatch(new Nano_Routes(), '');
	}

	protected function tearDown() {
		unset($_COOKIE['accept']);
	}

}