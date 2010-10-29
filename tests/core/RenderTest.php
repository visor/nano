<?php

class RenderTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Nano_Dispatcher
	 */
	private $dispatcher;

	protected function setUp() {
		$this->dispatcher = new Nano_Dispatcher();
	}

	public function testSimple() {
		$result = $this->dispatcher->run(Nano_Route::create('', 'test', 'test'));
		$this->assertEquals('test view rendered', $result);
	}

	public function testVithVariables() {
		$result = $this->dispatcher->run(Nano_Route::create('', 'test', 'test-var'));
		$this->assertEquals('Some title. 01=foo.03=bar.', $result);
	}

}