<?php

/**
 * @group core
 */
class Core_C_PluginTest extends TestUtils_TestCase implements Nano_C_Plugin {

	/**
	 * @var Nano_C
	 */
	protected $controller;

	protected $initWasRun, $beforeWasRun, $afterWasRun;

	/**
	 * @return void
	 * @param Nano_C $controller
	 */
	public function init(Nano_C $controller) {
		$this->initWasRun = true;
	}

	/**
	 * @return boolean
	 * @param Nano_C $controller
	 */
	public function before(Nano_C $controller) {
		$this->beforeWasRun = true;
	}

	/**
	 * @return void
	 * @param Nano_C $controller
	 */
	public function after(Nano_C $controller) {
		$this->afterWasRun = true;
	}

	protected function setUp() {
		include_once $this->files->get($this, '/TestController.php');

		$application = new Application();
		$application
			->withRootDir($GLOBALS['application']->rootDir)
			->withConfigurationFormat('php')
			->withPlugin($this)
			->configure()
		;
		$this->controller = new Core_C_TestController($application);
		$this->resetWasRun();
	}

	/**
	 * @cover Nano_C_Plugin
	 */
	public function testPluginMethodsShouldRuns() {
		self::assertNull($this->controller->run('example'));

		self::assertTrue($this->initWasRun);
		self::assertTrue($this->beforeWasRun);
		self::assertTrue($this->afterWasRun);
	}

	protected function tearDown() {
		$this->resetWasRun();
		unSet($this->controller);
	}

	protected function resetWasRun() {
		$this->initWasRun   = false;
		$this->beforeWasRun = false;
		$this->afterWasRun  = false;
	}

}