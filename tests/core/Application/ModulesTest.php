<?php

require_once __DIR__ . '/Abstract.php';

/**
 * @group core
 */
class Core_Application_ModulesTest extends Core_Application_Abstract {

	public function testDetectingModuleName() {
		self::assertFalse(\Nano\Modules::isModuleName(__CLASS__));
		self::assertFalse(\Nano\Modules::isModuleName('M\\ClassName'));
		self::assertFalse(\Nano\Modules::isModuleName('SomeModule_'));
		self::assertFalse(\Nano\Modules::isModuleName('SomeName_Module2'));

		self::assertTrue(\Nano\Modules::isModuleName('SomeName_Module'));
		self::assertTrue(\Nano\Modules::isModuleName('A_Module'));
	}

	public function testConvertingModuleNameToFolder() {
		self::assertEquals('example',         $this->application->modules->nameToFolder('Example_Module'));
		self::assertEquals('a-example',       $this->application->modules->nameToFolder('AExample_Module'));
		self::assertEquals('an-example',      $this->application->modules->nameToFolder('AnExample_Module'));
		self::assertEquals('other-module',    $this->application->modules->nameToFolder('OtherModule_Module'));
		self::assertEquals('someothermodule', $this->application->modules->nameToFolder('Someothermodule_Module'));
	}

	public function testNameToFolderShouldReturnPassedNameWhenModuleFolderPassed() {
		$this->application->withModule('some-module', $this->files->get($this, '/test'));
		self::assertEquals('some-module', $this->application->modules->nameToFolder('some-module'));
	}

	public function testNameToFolderShouldThrowExceptionWhenNotModuleNamespacePassed() {
		$this->setExpectedException('Application_Exception_InvalidModuleNamespace', 'Given namespace "some module" is not valid module namespace');
		$this->application->modules->nameToFolder('some module');
	}

	public function testDetectingApplicationModulesDir() {
		$this->application
			->withConfigurationFormat('php')
			->withRootDir($GLOBALS['application']->rootDir)
		;
		self::assertFalse($this->application->offsetExists('modulesDir'));

		$this->application->configure();
		self::assertEquals(
			$GLOBALS['application']->rootDir . DIRECTORY_SEPARATOR . \Nano\Application::MODULES_DIR_NAME
			, $this->application->modulesDir
		);
	}

	public function testDetectingSharedModulesDir() {
		$expected = $this->application->nanoRootDir . DIRECTORY_SEPARATOR . \Nano\Application::MODULES_DIR_NAME;
		self::assertFalse($this->application->offsetExists('sharedModulesDir'));

		$this->application
			->withConfigurationFormat('php')
			->withRootDir($GLOBALS['application']->rootDir)
			->configure()
		;

		self::assertEquals($expected, $this->application->sharedModulesDir);
	}

	public function testWithModuleShouldThrowExceptionWhenNotExistedPathPassed() {
		$this->setExpectedException('Application_Exception_PathNotFound', 'Path not found: ' . __FILE__ . DIRECTORY_SEPARATOR . 'not-exists');
		$this->application->withModule('module1', __FILE__ . DIRECTORY_SEPARATOR . 'not-exists');
	}

	public function testWithModuleShouldThrowExceptionWhenModleNotExistInPathPassed() {
		$this->setExpectedException('Application_Exception_PathNotFound', 'Path not found: ' . __DIR__ . DIRECTORY_SEPARATOR . 'module1');
		$this->application->withModule('module1', __DIR__ . DIRECTORY_SEPARATOR . 'module1');
	}

	public function testWithModuleShouldAddModuleAndPathWhenPassedBoth() {
		self::assertInstanceOf('\Nano\Application', $this->application->withModule('test', __DIR__));
		self::assertInstanceOf('\Nano\Modules', $this->application->modules);
		self::assertEquals(__DIR__, $this->application->modules->offsetGet('test'));
	}

	public function testGettingModules() {
		$first = $this->application->modules;
		self::assertInstanceOf('\Nano\Modules', $first);
		$this->application->withModule('test', __DIR__);
		self::assertSame($first, $this->application->modules);
	}

	public function testWithModuleShouldAddSharedModuleFirstIfExists() {
		$this->application
			->withConfigurationFormat('php')
			->withSharedModulesDir($this->files->get($this, '/shared-modules'))
			->withModulesDir($this->files->get($this, '/application-modules'))
		;
		self::assertInstanceOf('\Nano\Application', $this->application->withModule('module1'));
		self::assertInstanceOf('\Nano\Application', $this->application->withModule('module2'));

		self::assertInstanceOf('\Nano\Modules', $this->application->modules);
		self::assertEquals($this->files->get($this, '/shared-modules/module1'), $this->application->modules->offsetGet('module1'));
		self::assertEquals($this->files->get($this, '/shared-modules/module2'), $this->application->modules->offsetGet('module2'));
	}

	public function testWithModuleShouldAddApplicationModuleIfSharedNotExists() {
		$this->application
			->withSharedModulesDir($this->files->get($this, '/shared-modules'))
			->withModulesDir($this->files->get($this, '/application-modules'))
		;

		self::assertInstanceOf('\Nano\Application', $this->application->withModule('module1'));
		self::assertInstanceOf('\Nano\Application', $this->application->withModule('module2'));
		self::assertInstanceOf('\Nano\Application', $this->application->withModule('module3'));

		self::assertInstanceOf('\Nano\Modules', $this->application->modules);
		self::assertEquals($this->files->get($this, '/shared-modules/module1'), $this->application->modules->offsetGet('module1'));
		self::assertEquals($this->files->get($this, '/shared-modules/module2'), $this->application->modules->offsetGet('module2'));
		self::assertEquals($this->files->get($this, '/application-modules/module3'), $this->application->modules->offsetGet('module3'));
	}

	public function testWithModuleShouldThrowExceptionWhenNotPathAndNotApplicationAndSharedModule() {
		$this->setExpectedException('Application_Exception_ModuleNotFound', 'Module \'module6\' not found in application and shared modules');

		$this->application->withSharedModulesDir($this->files->get($this, '/shared-modules'));
		$this->application->withModulesDir($this->files->get($this, '/application-modules'));
		$this->application->withModule('module6');
	}

	public function testAppendShouldThrowExceptionWhenPathParamIsNull() {
		$this->setExpectedException('Application_Exception_ModuleNotFound', 'Module \'module\' not found in application and shared modules');

		$this->application->modules->append('module', null);
	}

	public function testPathes() {
		$this->application->withModule('default', $this->files->get($this, '/test'));

		self::assertEquals($this->files->get($this, '\\test'),       $this->application->modules->getPath('default', null));
		self::assertEquals($this->files->get($this, '/test\\views'), $this->application->modules->getPath('default', 'views'));
		self::assertEquals($this->files->get($this, '/test'),        $this->application->modules->getPath('default', null));
	}

	public function testActive() {
		self::assertFalse($this->application->modules->active('default'));
		self::assertFalse($this->application->modules->active('some'));
		self::assertFalse($this->application->modules->active('other'));

		$this->application->withModule('default', $this->files->get($this, '/test'));
		self::assertTrue($this->application->modules->active('default'));
		self::assertFalse($this->application->modules->active('some'));
		self::assertFalse($this->application->modules->active('other'));

		$this->application->withModule('some', $this->files->get($this, '/application-modules/module1'));
		self::assertTrue($this->application->modules->active('default'));
		self::assertTrue($this->application->modules->active('some'));
		self::assertFalse($this->application->modules->active('other'));

		$this->application->withModule('other', $this->files->get($this, '/application-modules/module2'));
		self::assertTrue($this->application->modules->active('default'));
		self::assertTrue($this->application->modules->active('some'));
		self::assertTrue($this->application->modules->active('other'));
	}

	public function testClassesAutoloading() {
		$this->application->withModule('test', $this->files->get($this, '/test'));

		self::assertTrue(class_exists('Test_Module\LibraryClass'));
		self::assertEquals('Test_Module\\LibraryClass', Test_Module\LibraryClass::name());
	}

	public function testDetectingsControllerClass() {
		$this->application->withModule('test', $this->files->get($this, '/test'));
		$route = Nano_Route_Abstract::create('some', 'class', 'index', 'test');
		self::assertEquals('Test_Module\\ClassController', $route->controllerClass());
	}

	public function testModuleRoutes() {
		$this->application
			->withConfigurationFormat('php')
			->withModule('test', $this->files->get($this, '/test'))
			->configure()
		;

		$routes     = new Nano_Routes();
		$route      = Nano_Route_Abstract::create('some', 'class', 'index', 'test');
		$dispatcher = $this->application->dispatcher;
		$response   = new Nano_C_Response_Test($this->application);

		$dispatcher->setResponse($response);
		$routes->addRoute('get', $route);

		self::assertNotNull($dispatcher->getRoute($routes, '/some'));
		self::assertEquals($route->controller(), $dispatcher->getRoute($routes, '/some')->controller());
		self::assertEquals($route->action(),     $dispatcher->getRoute($routes, '/some')->action());
		self::assertInstanceOf('Test_Module\\ClassController', $dispatcher->getController($route));

		$dispatcher->dispatch($routes, '/some');
		self::assertTrue(class_exists('Test_Module\\ClassController', false));
		self::assertEquals(Test_Module\ClassController::name(), $response->getBody());
	}

	public function testModuleViews() {
		$this->application
			->withConfigurationFormat('php')
			->withModule('test', $this->files->get($this, '/test'))
			->configure()
		;
		self::assertTrue($this->application->loader->loadClass('Test_Module\\ClassController'));

		$response = new Nano_C_Response_Test($this->application);

		$this->application->dispatcher->setResponse($response);
		$this->application->dispatcher->run(Nano_Route_Abstract::create('', 'class', 'view', 'test'));

		self::assertEquals('view action runned', $response->getBody());
	}

	public function testGetPathShouldThrowExceptionWhenModuleNotExists() {
		$this->setExpectedException('Application_Exception_ModuleNotFound', 'Module \'some module\' not found');
		$this->application->modules->getPath('some module');
	}

}