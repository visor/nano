<?php

/**
 * @group core
 */
class Core_HelperBrokerTest extends TestUtils_TestCase {

	/**
	 * @var Nano_HelperBroker
	 */
	protected $helper;

	protected function setUp() {
		$application  = new Application();
		$application
			->withConfigurationFormat('php')
			->withRootDir($GLOBALS['application']->rootDir)
			->withModule('example', $this->files->get($this, '/example'))
			->withModule('another-example', $this->files->get($this, '/another-example'))
			->configure()
		;
		$this->helper = $application->helper;
	}

	public function testShouldThrowExceptionWhenModuleNotFound() {
		$this->setExpectedException('Application_Exception_ModuleNotFound', 'Module \'some-module\' not found in application and shared modules');
		$this->helper->someModule;
	}

	public function testShouldThrowExceptionWhenModuleHelperFileNotFound() {
		$this->setExpectedException('Nano_Exception', 'Helper example\\notfound not found');
		$this->helper->example->notFound;
	}

	public function testShouldThrowExceptionWhenModuleHelperNotLoaded() {
		$this->setExpectedException('Nano_Exception', 'Helper example\\wrong not found');
		$this->helper->example->wrong;
	}

	public function testShouldThrowExceptionWhenApplicationHelperNotLoaded() {
		$this->setExpectedException('Nano_Exception', 'Helper notfound not found');
		$this->helper->notFound();
	}

	public function testSearchingApplicationHelper() {
		self::assertInstanceOf('CounterHelper', $this->helper->counter());
	}

	public function testSearchingModuleClasses() {
		$helper = $this->helper->example;
		self::assertInstanceOf('Nano_HelperBroker_Module', $helper);
		self::assertInstanceOf('\\Example_Module\\SomeHelper', $helper->some);
		self::assertInstanceOf('\\Example_Module\\SomeHelper', $helper->some());
		self::assertEquals('Example_Module\\SomeHelper', $helper->some->work());
		self::assertEquals('Example_Module\\SomeHelper', $helper->some()->work());

		$helper = $this->helper->anotherExample;
		self::assertInstanceOf('Nano_HelperBroker_Module', $helper);
		self::assertInstanceOf('\\AnotherExample_Module\\SomeHelper', $helper->some);
		self::assertInstanceOf('\\AnotherExample_Module\\SomeHelper', $helper->some());
		self::assertEquals('example content', $helper->some->work());
		self::assertEquals('example content', $helper->some()->work());
	}

	public function testShouldReturnSameInstancesForOneModuleHelper() {
		self::assertSame($this->helper->example, $this->helper->example);
	}

	public function testShouldReturnSameInstancesForOneApplicationHelper() {
		self::assertSame($this->helper->counter(), $this->helper->counter());
	}

	protected function tearDown() {
		unSet($this->helper);
	}

}