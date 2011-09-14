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
		include_once $this->files->get($this, '/FakePlugin.php');

		$plugin = new Core_Application_FakePlugin();
		self::assertNull(self::getObjectProperty($this->application, 'plugins'));
		self::assertInstanceOf('Application', $this->application->withPlugin($plugin));
		self::assertInstanceOf('SplObjectStorage', self::getObjectProperty($this->application, 'plugins'));

		/** @var Nano_C_Plugin[]|SplObjectStorage $plugins */
		$plugins = self::getObjectProperty($this->application, 'plugins');

		self::assertEquals(1, $plugins->count());
		self::assertTrue($plugins->contains($plugin));
	}

	public function testGettingPlugins() {
		self::assertNull(self::getObjectProperty($this->application, 'plugins'));
		self::assertInstanceOf('SplObjectStorage', $this->application->getPlugins());
		self::assertInstanceOf('SplObjectStorage', self::getObjectProperty($this->application, 'plugins'));
	}

	protected function tearDown() {
		self::setObjectProperty('Application', 'current', null);
		unSet($this->application);
	}

}
