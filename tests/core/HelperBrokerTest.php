<?php

/**
 * @group core
 */
class Core_HelperBrokerTest extends TestUtils_TestCase {

	/**
	 * @var \Nano\Application
	 */
	protected $application;

	/**
	 * @var Nano_HelperBroker
	 */
	protected $helper;

	protected function setUp() {
		$this->app->backup();

		$this->application = new \Nano\Application();
		$this->application
			->withConfigurationFormat('php')
			->withRootDir($GLOBALS['application']->rootDir)
			->withModule('example', __DIR__ . '/_files/example')
			->withModule('another-example', __DIR__ . '/_files/another-example')
			->configure()
		;
		$this->helper = $this->application->helper;
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
		self::assertInstanceOf('Module\Example\Helper\Some', $helper->some);
		self::assertInstanceOf('Module\Example\Helper\Some', $helper->some());
		self::assertEquals('Module\Example\Helper\Some', $helper->some->work());
		self::assertEquals('Module\Example\Helper\Some', $helper->some()->work());

		$helper = $this->helper->anotherExample;
		self::assertInstanceOf('Nano_HelperBroker_Module', $helper);
		self::assertInstanceOf('Module\AnotherExample\Helper\Some', $helper->some);
		self::assertInstanceOf('Module\AnotherExample\Helper\Some', $helper->some());
		self::assertEquals('example content', $helper->some->work());
		self::assertEquals('example content', $helper->some()->work());
	}

	public function testShouldReturnSameInstancesForOneModuleHelper() {
		self::assertSame($this->helper->example, $this->helper->example);
	}

	public function testSearchingApplicationHelper() {
		self::assertInstanceOf('App\Helper\Counter', $this->helper->counter());
	}

	public function testShouldReturnSameInstancesForOneApplicationHelper() {
		self::assertSame($this->helper->counter(), $this->helper->counter());
	}

	protected function tearDown() {
		unSet($this->helper, $this->application);
		$this->app->restore();
	}

}