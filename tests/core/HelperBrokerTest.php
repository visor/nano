<?php

/**
 * @group framework
 * @group helpers
 */
class Core_HelperBrokerTest extends TestUtils_TestCase {

	/**
	 * @var Nano_HelperBroker
	 */
	protected $helper;

	protected function setUp() {
		$this->helper = new Nano_HelperBroker();
		$application = new Application();
		$dispatcher  = new Nano_Dispatcher($application);
		$application->withModule('example', $this->files->get($this, '/example'));

		$this->helper->setDispatcher($dispatcher);
	}

	public function testSearchingModuleClasses() {
		$helper = $this->helper->get('example');
		self::assertInstanceOf('Example_Module\\ExampleHelper', $helper);
	}

	protected function tearDown() {
		unSet($this->helper);
	}

}