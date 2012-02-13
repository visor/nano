<?php

/**
 * @group core
 * @group cli
 */
class Core_Cli_CommonTest extends TestUtils_TestCase {

	/**
	 * @var string
	 */
	protected $cwd, $appRoot, $nanoRoot;

	/**
	 * @var Nano_Cli
	 */
	protected $cli;

	protected function setUp() {
		$application = new Application();
		$application
			->withRootDir($GLOBALS['application']->rootDir)
			->withConfigurationFormat('php')
			->configure()
		;

		$this->appRoot  = dirName(__DIR__) . '/Application/_files';
		$this->nanoRoot = $application->nanoRootDir;
		$this->cwd      = getCwd();
		$this->cli      = new Nano_Cli();
		chDir($this->appRoot);
	}

	public function testIsWindows() {
		self::assertEquals('\\' == DIRECTORY_SEPARATOR, Nano_Cli::isWindows());
	}

	public function testDetectingApplicationDirectoryInBootstrapDir() {
		self::assertNull(self::getObjectProperty($this->cli, 'applicationDir'));

		self::assertEquals(0, $this->cli->run(array()));
		self::assertEquals($this->appRoot, self::getObjectProperty($this->cli, 'applicationDir'));
		self::assertInstanceOf('Application', $this->cli->getApplication());
	}

	public function testDetectingApplicationDirectoryInSubdir() {
		chDir($this->appRoot . '/application-modules/module1');

		self::assertNull(self::getObjectProperty($this->cli, 'applicationDir'));
		self::assertEquals(0, $this->cli->run(array()));
		self::assertEquals($this->appRoot, self::getObjectProperty($this->cli, 'applicationDir'));
		self::assertInstanceOf('Application', $this->cli->getApplication());
	}

	public function testApplicationShouldBeNullIfBootstrapIfEmpty() {
		chDir($this->files->get($this, '/null-file'));
		self::assertEquals(0, $this->cli->run(array()));
		self::assertNull($this->cli->getApplication());
	}

	public function testApplicationShouldBeNullIfBootstrapLoadFails() {
		chDir($this->files->get($this, '/return-false'));
		self::assertEquals(0, $this->cli->run(array()));
		self::assertNull($this->cli->getApplication());
	}

	public function testApplicationShouldBeNullIfAnotherClassInstance() {
		chDir($this->files->get($this, '/not-instance'));
		self::assertEquals(0, $this->cli->run(array()));
		self::assertNull($this->cli->getApplication());
	}

	public function testDefaultScriptsShouldBeLoaded() {
		$expected = array();
		$iterator = new DirectoryIterator($this->nanoRoot . DIRECTORY_SEPARATOR . Nano_Cli::DIR);
		foreach ($iterator as /** @var DirectoryIterator $item */ $item) {
			if ($item->isDir() || $item->isDot()) {
				continue;
			}
			$expected[] = $item->getBasename('.php');
		}
		unSet($iterator, $item);

		self::assertEquals(0, $this->cli->run(array()));

		/** @var ArrayObject $actual */
		$actual = $this->cli->getScripts();
		foreach ($expected as $name) {
			self::assertTrue($actual->offsetExists($name));
		}
	}

	public function testApplicationScriptsShouldBeLoadedIfExists() {
		self::assertEquals(0, $this->cli->run(array()));
		/** @var ArrayObject $actual */
		$actual = $this->cli->getScripts();
		self::assertTrue($actual->offsetExists('test-script'));
	}

	public function testModulesScriptsShouldBeLoadedIfExists() {
		self::assertEquals(0, $this->cli->run(array()));

		/** @var ArrayObject $actual */
		$actual = $this->cli->getScripts();
		self::assertTrue($actual->offsetExists('module-three-script'));
		self::assertTrue($actual->offsetExists('module-five-script'));
	}

	public function testOnlyProperlyNamedScriptsShouldBeLoaded() {
		self::assertEquals(0, $this->cli->run(array()));
		/** @var ArrayObject $actual */
		$actual = $this->cli->getScripts();

		self::assertFalse($actual->offsetExists('test_script'));
		self::assertFalse($actual->offsetExists('script1'));
		self::assertFalse($actual->offsetExists('another-script'));
		self::assertFalse($actual->offsetExists('not-php-ext'));
	}

	public function testNotScriptFilesShouldNotLoaded() {
		self::assertEquals(0, $this->cli->run(array()));
		/** @var ArrayObject $actual */
		self::assertNull($this->cli->getScript('abstract-script'));
		self::assertNull($this->cli->getScript('not-script-child'));
	}

	public function testGetScriptShouldReturnNullIfScriptNotExists() {
		self::assertNull($this->cli->getScript('test-script'));
		self::assertEquals(0, $this->cli->run(array()));
		self::assertNull($this->cli->getScript('another-script'));
	}

	public function testGetScriptShouldReturnScriptIfExists() {
		self::assertEquals(0, $this->cli->run(array()));
		self::assertInstanceOf('ReflectionClass', $this->cli->getScript('test-script'));
	}

	public function testRunShouldReturn1WhenScriptNotFound() {
		self::assertEquals(1, $this->cli->run(array('script-not-found')));
	}

	public function testRunShouldReturn2WhenScriptNeedApplicationButNotSpecified() {
		chdir(__DIR__);
		self::assertEquals(2, $this->cli->run(array('setup')));
	}

	public function testRunShouldReturnScriptResultAfterExecute() {
		self::assertEquals(100, $this->cli->run(array('test-script')));
		self::assertContains('[test script was run]', $this->getActualOutput());
	}

	public function testRunWrapper() {
		self::assertEquals(0, Nano_Cli::main(array()));
	}

	protected function tearDown() {
		chDir($this->cwd);
	}

}