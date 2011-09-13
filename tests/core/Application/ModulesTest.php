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
				$application->withModule('module1', __DIR__);
			}
			, 'Application_Exception_PathNotFound'
			, 'Path not found: ' . __DIR__ . DIRECTORY_SEPARATOR . 'module1'
		);
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

	public function testAddingModules() {
		self::markTestIncomplete('Not implemented yet');
	}

	public function testGettingModules() {
		self::markTestIncomplete('Not implemented yet');
	}

	protected function tearDown() {
		self::setObjectProperty('Application', 'current', null);
		unSet($this->application);
	}

}