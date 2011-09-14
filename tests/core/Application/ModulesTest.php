<?php

/**
 * @group core
 * @group core-application
 */
class Core_Application_ModulesTest extends TestUtils_TestCase {

	/**
	 * @var Application
	 */
	protected $application;

	protected function setUp() {
		self::setObjectProperty('Application', 'current', null);
		$this->application = new Application();
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

	protected function tearDown() {
		self::setObjectProperty('Application', 'current', null);
		unSet($this->application);
	}

}