<?php

/**
 * @group framework
 */
class NanoModulesTest extends TestUtils_TestCase {

	/**
	 * @var Nano_Modules
	 */
	private $modules;

	protected function setUp() {
		$this->modules = new Nano_Modules();
		Nano::modules()->append('test-module', $this->files->get($this, '/test-module'));
	}

	public function testPathes() {
		$this->modules
			->append('default')
			->append('other', DS . 'tmp')
			->append('some', DS . 'path2')
		;
		self::assertEquals(DS . 'tmp', $this->modules->getPath('other', null));
		self::assertEquals(DS . 'path2', $this->modules->getPath('some', null));
		self::assertEquals(DS . 'path2' . DS .'views', $this->modules->getPath('some', 'views'));
		self::assertEquals(MODULES . DS  . 'default', $this->modules->getPath('default', null));
	}

	public function testActive() {
		self::assertFalse($this->modules->active('default'));
		self::assertFalse($this->modules->active('some'));
		self::assertFalse($this->modules->active('other'));

		$this->modules->append('default');
		self::assertTrue($this->modules->active('default'));
		self::assertFalse($this->modules->active('some'));
		self::assertFalse($this->modules->active('other'));

		$this->modules->append('some', DS . 'path2');
		self::assertTrue($this->modules->active('default'));
		self::assertTrue($this->modules->active('some'));
		self::assertFalse($this->modules->active('other'));

		$this->modules->append('other', DS . 'tmp');
		self::assertTrue($this->modules->active('default'));
		self::assertTrue($this->modules->active('some'));
		self::assertTrue($this->modules->active('other'));
	}

	public function testClassesAutoloading() {
		self::assertEquals('M_TestModule_Library_Class', M_TestModule_Library_Class::name());
	}

	public function testDetectingsControllerClass() {
		$route = Nano_Route::create('some', 'class', 'index', 'test-module');
		self::assertEquals('M_TestModule_Controller_Class', $route->controllerClass());
	}

	public function testModuleRoutes() {
		$routes     = new Nano_Routes();
		$dispatcher = new Nano_Dispatcher();
		$route      = Nano_Route::create('some', 'class', 'index', 'test-module');

		$routes->addRoute('get', $route);

		self::assertNotNull($dispatcher->getRoute($routes, '/some'));
		self::assertEquals($route->controller(), $dispatcher->getRoute($routes, '/some')->controller());
		self::assertEquals($route->action(), $dispatcher->getRoute($routes, '/some')->action());

		self::assertNotNull($dispatcher->getController($route));

		$result = $dispatcher->dispatch($routes, '/some');
		self::assertTrue(class_exists('M_TestModule_Controller_Class', false));
		self::assertEquals(M_TestModule_Controller_Class::name(), $result);
	}

	public function testModuleViews() {
		Nano::modules()->append('test-module', $this->files->get($this, DS . 'test-module'));
		self::assertTrue(Nano_Loader::load('M_TestModule_Controller_Class'));

		$dispatcher = new Nano_Dispatcher();
		self::assertEquals('view action runned', $dispatcher->run(Nano_Route::create('', 'class', 'view', 'test-module')));
	}

	protected function tearDown() {
		$this->modules = null;
		if (Nano::modules()->active('test-module')) {
			Nano::modules()->offsetUnset('test-module');
		}
	}

}