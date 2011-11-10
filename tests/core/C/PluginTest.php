<?php

/**
 * @group core
 */
class Nano_C_PluginTest extends TestUtils_TestCase implements Nano_C_Plugin {

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
		$this->controller = new Core_C_TestController(new Nano_Dispatcher(Application::current()));
		$this->resetWasRun();
		Application::current()->withPlugin($this);
	}

	/**
	 * @cover Nano_C_Plugin
	 */
	public function testPluginMethodsShouldRuns() {
		self::assertNull($this->controller->run('example'));

		self::assertTrue($this->initWasRun);
		self::assertTrue($this->beforeWasRun);
		self::assertTrue($this->afterWasRun);
		Application::current()->getPlugins()->detach($this);
	}

	protected function tearDown() {
		unSet($this->controller);
		$this->resetWasRun();
	}

	protected function resetWasRun() {
		$this->initWasRun   = false;
		$this->beforeWasRun = false;
		$this->afterWasRun  = false;
	}

}