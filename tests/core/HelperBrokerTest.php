<?php

/**
 * @group core
 */
class Core_HelperBrokerTest extends TestUtils_TestCase {

	/**
	 * @var Application
	 */
	protected static $application;

	/**
	 * @var Nano_HelperBroker
	 */
	protected $helper;

	public static function setUpBeforeClass() {
		self::$application = new Application();
		self::$application
			->withConfigurationFormat('php')
			->withRootDir($GLOBALS['application']->rootDir)
			->withModule('example', __DIR__ . '/_files/example')
			->withModule('another-example', __DIR__ . '/_files/another-example')
			->configure()
		;
	}

	protected function setUp() {
		$this->helper = self::$application->helper;
	}

	public function testShouldThrowExceptionWhenModuleNotFound() {
		$this->setExpectedException('Application_Exception_ModuleNotFound', 'Module \'some-module\' not found in application and shared modules');
		$this->helper->someModule;
	}

	public function testShouldThrowExceptionWhenModuleHelperNotLoaded() {
		$this->setExpectedException('Nano_Exception_HelperNotFound', 'Helper wrong in module example not found');
		$this->helper->example->wrong;
	}

	public function testShouldThrowExceptionWhenApplicationHelperNotLoaded() {
		$this->setExpectedException('Nano_Exception_HelperNotFound', 'Helper notfound not found');
		$this->helper->notFound();
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

	public function testSearchingApplicationHelper() {
		self::assertInstanceOf('CounterHelper', $this->helper->counter());
	}

	public function testShouldReturnSameInstancesForOneApplicationHelper() {
		self::assertSame($this->helper->counter(), $this->helper->counter());
	}

	protected function tearDown() {
		unSet($this->helper);
	}

	public static function tearDownAfterClass() {
		self::$application = null;
	}

}