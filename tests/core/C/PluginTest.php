<?php

/**
 * @group core
 */
class Core_C_PluginTest extends \Nano\TestUtils\TestCase implements \Nano\Controller\Plugin {

	/**
	 * @var \Nano\Controller
	 */
	protected $controller;

	protected $initWasRun, $beforeWasRun, $afterWasRun;

	/**
	 * @return void
	 * @param \Nano\Controller $controller
	 */
	public function init(\Nano\Controller $controller) {
		$this->initWasRun = true;
	}

	/**
	 * @return boolean
	 * @param \Nano\Controller $controller
	 */
	public function before(\Nano\Controller $controller) {
		$this->beforeWasRun = true;
	}

	/**
	 * @return void
	 * @param \Nano\Controller $controller
	 */
	public function after(\Nano\Controller $controller) {
		$this->afterWasRun = true;
	}

	protected function setUp() {
		$this->app->backup();
		include_once $this->files->get($this, '/TestController.php');

		$application = new \Nano\Application();
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
	 * @cover \Nano\Controller\Plugin
	 */
	public function testPluginMethodsShouldRuns() {
		self::assertNull($this->controller->run('example'));

		self::assertTrue($this->initWasRun);
		self::assertTrue($this->beforeWasRun);
		self::assertTrue($this->afterWasRun);
	}

	protected function resetWasRun() {
		$this->initWasRun   = false;
		$this->beforeWasRun = false;
		$this->afterWasRun  = false;
	}

	protected function tearDown() {
		$this->resetWasRun();
		unSet($this->controller);
		$this->app->restore();
	}

}