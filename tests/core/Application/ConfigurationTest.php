<?php

require_once __DIR__ . '/Abstract.php';

/**
 * @group core
 */
class Core_Application_ConfigurationTest extends Core_Application_Abstract {

	public function testFactoryMethod() {
		self::assertInstanceOf('Application', Application::create());
		self::assertNotSame(Application::create(), Application::create());
	}

	public function testConfigureShouldThrowExceptionWhenNoConfigurationFormatSpecified() {
		$this->setExpectedException('Application_Exception_InvalidConfiguration', 'Configuration format not specified');
		$this->application->configure();
	}

	public function testSettingApplicationRootDir() {
		self::assertInstanceOf('Application', $this->application->withRootDir('/some/path'));
		self::assertEquals('/some/path', $this->application->rootDir);
	}

	public function testDetectingDefaultApplicationRootDir() {
		self::assertFalse($this->application->offsetExists('rootDir'));
		$this->application
			->withConfigurationFormat('php')
			->configure()
		;
		self::assertEquals(getCwd(), $this->application->rootDir);
	}

	public function testDetectingNanoDir() {
		$expected = realPath(__DIR__ . '/../../../');
		self::assertEquals($expected, $this->application->nanoRootDir);
	}

	public function testDetectingPublicDir() {
		$expected = $GLOBALS['application']->rootDir . DIRECTORY_SEPARATOR . Application::PUBLIC_DIR_NAME;
		$this->application
			->withConfigurationFormat('php')
			->withRootDir($GLOBALS['application']->rootDir)
		;

		self::assertFalse($this->application->offsetExists('publicDir'));
		$this->application->configure();
		self::assertTrue($this->application->offsetExists('publicDir'));
		self::assertEquals($expected, $this->application->publicDir);
	}

	public function testConfigurationFormat() {
		$expected = 'Nano_Config_Format_Json';
		self::assertFalse($this->application->offsetExists('configFormat'));
		self::assertInstanceOf('Application', $this->application->withConfigurationFormat('json'));
		self::assertInstanceOf($expected, $this->application->configFormat);
	}

	public function testAddingPugins() {
		include_once $this->files->get($this, '/FakePlugin.php');

		$plugin = new Core_Application_FakePlugin();

		self::assertTrue($this->application->offsetExists('plugins'));
		self::assertInstanceOf('Application', $this->application->withPlugin($plugin));
		self::assertInstanceOf('SplObjectStorage', $this->application->plugins);

		self::assertEquals(1, $this->application->plugins->count());
		self::assertTrue($this->application->plugins->contains($plugin));
	}

	public function testGettingPlugins() {
		self::assertTrue($this->application->offsetExists('plugins'));
		self::assertInstanceOf('SplObjectStorage', $this->application->plugins);
	}

}
