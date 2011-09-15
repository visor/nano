<?php

require_once __DIR__ . '/Abstract.php';

/**
 * @group core
 * @group core-application
 */
class Core_Application_ModulesTest extends Core_Application_Abstract {

	public function testDetectingModuleName() {
		self::assertFalse(Nano_Modules::isModuleName(__CLASS__));
		self::assertFalse(Nano_Modules::isModuleName('M\\ClassName'));
		self::assertFalse(Nano_Modules::isModuleName('SomeModule_'));
		self::assertFalse(Nano_Modules::isModuleName('SomeName_Module2'));

		self::assertTrue(Nano_Modules::isModuleName('SomeName_Module'));
		self::assertTrue(Nano_Modules::isModuleName('A_Module'));
	}

	public function testConvertingModuleNameToFolder() {
		self::assertEquals('example',         $this->application->getModules()->nameToFolder('Example_Module'));
		self::assertEquals('a-example',       $this->application->getModules()->nameToFolder('AExample_Module'));
		self::assertEquals('an-example',      $this->application->getModules()->nameToFolder('AnExample_Module'));
		self::assertEquals('other-module',    $this->application->getModules()->nameToFolder('OtherModule_Module'));
		self::assertEquals('someothermodule', $this->application->getModules()->nameToFolder('Someothermodule_Module'));
	}

	public function testNameToFolderShouldReturnPassedNameWhenModuleFolderPassed() {
		$this->application->withModule('some-module', $this->files->get($this, '/test'));
		self::assertEquals('some-module', $this->application->getModules()->nameToFolder('some-module'));
	}

	public function testNameToFolderShouldThrowExceptionWhenNotModuleNamespacePassed() {
		$this->setExpectedException('Application_Exception_InvalidModuleNamespace', 'Given namespace "some module" is not valid module namespace');
		$this->application->getModules()->nameToFolder('some module');
	}

	public function testDetectingApplicationModulesDir() {
		$this->application->withRootDir(__DIR__);
		self::assertNull(self::getObjectProperty($this->application, 'modulesDir'));
		self::assertEquals(
			__DIR__ . DIRECTORY_SEPARATOR . Application::MODULES_DIR_NAME
			, $this->application->getModulesDir()
		);
	}

	public function testDetectingSharedModulesDir() {
		$expected = getCwd() . DIRECTORY_SEPARATOR . Application::MODULES_DIR_NAME;
		self::assertNull(self::getObjectProperty($this->application, 'sharedModulesDir'));
		self::assertEquals($expected, $this->application->getSharedModulesDir());
		self::assertEquals($expected, self::getObjectProperty($this->application, 'sharedModulesDir'));
	}

	public function testWithModuleShouldThrowExceptionWhenNotExistedPathPassed() {
		$application = $this->application;
		self::assertException(
			function() use ($application) {
				/** @var Application $application */
				$application->withModule('module1', __FILE__ . DIRECTORY_SEPARATOR . 'not-exists');
			}
			, 'Application_Exception_PathNotFound'
			, 'Path not found: ' . __FILE__ . DIRECTORY_SEPARATOR . 'not-exists'
		);
	}

	public function testWithModuleShouldThrowExceptionWhenModleNotExistInPathPassed() {
		$application = $this->application;
		self::assertException(
			function() use ($application) {
				/** @var Application $application */
				$application->withModule('module1', __DIR__ . DIRECTORY_SEPARATOR . 'module1');
			}
			, 'Application_Exception_PathNotFound'
			, 'Path not found: ' . __DIR__ . DIRECTORY_SEPARATOR . 'module1'
		);
	}

	public function testWithModuleShouldAddModuleAndPathWhenPassedBoth() {
		self::assertInstanceOf('Application', $this->application->withModule('test', __DIR__));
		self::assertInstanceOf('Nano_Modules', self::getObjectProperty($this->application, 'modules'));
		self::assertEquals(__DIR__, self::getObjectProperty($this->application, 'modules')->offsetGet('test'));
	}

	public function testGettingModules() {
		$first = $this->application->getModules();
		self::assertInstanceOf('Nano_Modules', $first);
		$this->application->withModule('test', __DIR__);
		self::assertSame($first, $this->application->getModules());
	}

	public function testWithModuleShouldAddSharedModuleFirstIfExists() {
		$this->application->withSharedModulesDir($this->files->get($this, '/shared-modules'));
		self::assertInstanceOf('Application', $this->application->withModule('module1'));
		self::assertInstanceOf('Application', $this->application->withModule('module2'));

		self::assertInstanceOf('Nano_Modules', self::getObjectProperty($this->application, 'modules'));
		self::assertEquals($this->files->get($this, '/shared-modules/module1'), self::getObjectProperty($this->application, 'modules')->offsetGet('module1'));
		self::assertEquals($this->files->get($this, '/shared-modules/module2'), self::getObjectProperty($this->application, 'modules')->offsetGet('module2'));
	}

	public function testWithModuleShouldAddApplicationModuleIfSharedNotExists() {
		$this->application->withSharedModulesDir($this->files->get($this, '/shared-modules'));
		$this->application->withModulesDir($this->files->get($this, '/application-modules'));

		self::assertInstanceOf('Application', $this->application->withModule('module1'));
		self::assertInstanceOf('Application', $this->application->withModule('module2'));
		self::assertInstanceOf('Application', $this->application->withModule('module3'));

		self::assertInstanceOf('Nano_Modules', self::getObjectProperty($this->application, 'modules'));
		self::assertEquals($this->files->get($this, '/shared-modules/module1'), self::getObjectProperty($this->application, 'modules')->offsetGet('module1'));
		self::assertEquals($this->files->get($this, '/shared-modules/module2'), self::getObjectProperty($this->application, 'modules')->offsetGet('module2'));
		self::assertEquals($this->files->get($this, '/application-modules/module3'), self::getObjectProperty($this->application, 'modules')->offsetGet('module3'));
	}

	public function testWithModuleShouldThrowExceptionWhenNotPathAndNotApplicationAndSharedModule() {
		$this->application->withSharedModulesDir($this->files->get($this, '/shared-modules'));
		$this->application->withModulesDir($this->files->get($this, '/application-modules'));
		$application = $this->application;

		self::assertException(
			function() use ($application) {
				/** @var Application $application */
				$application->withModule('module4');
			}
			, 'Application_Exception_ModuleNotFound'
			, 'Module \'module4\' not found in application and shared modules'
		);
	}

	public function testPathes() {
		$this->application->withModule('default', $this->files->get($this, '/test'));

		self::assertEquals($this->files->get($this, '\\test'),       $this->application->getModules()->getPath('default', null));
		self::assertEquals($this->files->get($this, '/test\\views'), $this->application->getModules()->getPath('default', 'views'));
		self::assertEquals($this->files->get($this, '/test'),        $this->application->getModules()->getPath('default', null));
	}

	public function testActive() {
		self::assertFalse($this->application->getModules()->active('default'));
		self::assertFalse($this->application->getModules()->active('some'));
		self::assertFalse($this->application->getModules()->active('other'));

		$this->application->withModule('default', $this->files->get($this, '/test'));
		self::assertTrue($this->application->getModules()->active('default'));
		self::assertFalse($this->application->getModules()->active('some'));
		self::assertFalse($this->application->getModules()->active('other'));

		$this->application->withModule('some', $this->files->get($this, '/application-modules/module1'));
		self::assertTrue($this->application->getModules()->active('default'));
		self::assertTrue($this->application->getModules()->active('some'));
		self::assertFalse($this->application->getModules()->active('other'));

		$this->application->withModule('other', $this->files->get($this, '/application-modules/module2'));
		self::assertTrue($this->application->getModules()->active('default'));
		self::assertTrue($this->application->getModules()->active('some'));
		self::assertTrue($this->application->getModules()->active('other'));
	}

	public function testClassesAutoloading() {
		$this->application->withModule('test', $this->files->get($this, '/test'));
		self::assertEquals('Test_Module\\LibraryClass', Test_Module\LibraryClass::name());
	}

	public function testDetectingsControllerClass() {
		$this->application->withModule('test', $this->files->get($this, '/test'));
		$route = Nano_Route::create('some', 'class', 'index', 'test');
		self::assertEquals('Test_Module\\ClassController', $route->controllerClass());
	}

	public function testModuleRoutes() {
		$this->application->withModule('test', $this->files->get($this, '/test'));

		$routes     = new Nano_Routes();
		$dispatcher = new Nano_Dispatcher($this->application);
		$route      = Nano_Route::create('some', 'class', 'index', 'test');

		$routes->addRoute('get', $route);

		self::assertNotNull($dispatcher->getRoute($routes, '/some'));
		self::assertEquals($route->controller(), $dispatcher->getRoute($routes, '/some')->controller());
		self::assertEquals($route->action(),     $dispatcher->getRoute($routes, '/some')->action());
		self::assertInstanceOf('Test_Module\\ClassController', $dispatcher->getController($route));

		$result = $dispatcher->dispatch($routes, '/some');
		self::assertTrue(class_exists('Test_Module\\ClassController', false));
		self::assertEquals(Test_Module\ClassController::name(), $result);
	}

	public function testModuleViews() {
		$this->application->withModule('test', $this->files->get($this, '/test'));
		self::assertTrue($this->application->loader()->loadClass('Test_Module\\ClassController'));

		$dispatcher = new Nano_Dispatcher($this->application);
		self::assertEquals('view action runned', $dispatcher->run(Nano_Route::create('', 'class', 'view', 'test')));
	}

	public function testGetPathShouldThrowExceptionWhenModuleNotExists() {
		$this->setExpectedException('Application_Exception_ModuleNotFound', 'Module \'some module\' not found');
		$this->application->getModules()->getPath('some module');
	}

}