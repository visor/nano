<?php

/**
 * @group core
 * @group core-application
 */
class Core_Application_ConfigurationTest extends TestUtils_TestCase {

	/**
	 * @var Application
	 */
	protected $application;

	protected function setUp() {
		self::setObjectProperty('Application', 'current', null);
		$this->application = new Application();
	}

	public function testGettingCurrent() {
		self::assertNull(Application::current());
		$this->application = Application::configure();
		self::assertInstanceOf('Application', Application::current());
		self::assertSame($this->application, Application::current());
	}

	public function testApplicationRootDir() {
		self::assertInstanceOf('Application', $this->application->withRootDir('/some/path'));
		self::assertEquals('/some/path', self::getObjectProperty($this->application, 'rootDir'));
	}

	public function testDetectingDefaultApplicationRootDir() {
		self::assertNull(self::getObjectProperty($this->application, 'rootDir'));
		self::assertEquals(getCwd(), $this->application->getRootDir());
		self::assertEquals(getCwd(), self::getObjectProperty($this->application, 'rootDir'));
	}

	public function testDetectingNanoDir() {
		self::assertNull(self::getObjectProperty($this->application, 'nanoRootDir'));
		self::assertEquals(getCwd(), $this->application->getNanoRootDir());
		self::assertEquals(getCwd(), self::getObjectProperty($this->application, 'nanoRootDir'));
	}

	public function testDetectingPublicDir() {
		$expected = __DIR__ . DIRECTORY_SEPARATOR . Application::PUBLIC_DIR_NAME;
		$this->application->withRootDir(__DIR__);
		self::assertNull(self::getObjectProperty($this->application, 'publicDir'));
		self::assertEquals($expected, $this->application->getPublicDir());
		self::assertEquals($expected, self::getObjectProperty($this->application, 'publicDir'));
	}

	public function testConfigurationFormat() {
		$expected = 'Nano_Config_Format_Json';
		self::assertNull(self::getObjectProperty($this->application, 'configFormat'));
		self::assertInstanceOf('Application', $this->application->usingConfigurationFormat('json'));
		self::assertInstanceOf($expected, self::getObjectProperty($this->application, 'configFormat'));
		self::assertInstanceOf($expected, $this->application->getConfigurationFormat());

		$expected = 'Nano_Config_Format_Php';
		self::setObjectProperty($this->application, 'configFormat', null);
		self::assertInstanceOf($expected, $this->application->getConfigurationFormat());
		self::assertInstanceOf($expected, self::getObjectProperty($this->application, 'configFormat'));
	}

	public function testAddingPugins() {
		self::markTestIncomplete('Not implemented yet');
	}

	public function testGettingPlugins() {
		self::markTestIncomplete('Not implemented yet');
	}

	protected function tearDown() {
		self::setObjectProperty('Application', 'current', null);
		unSet($this->application);
	}

}
