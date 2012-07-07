<?php

abstract class Core_Config_Format_TestAbstract extends TestUtils_TestCase  {

	/**
	 * @var string
	 */
	protected $configFile, $routesFile, $resultFile;

	/**
	 * @var \Nano\Application\Config\Format
	 */
	protected $config;

	/**
	 * @return \Nano\Application\Config\Format
	 */
	abstract protected function getConfigInstance();

	/**
	 * @return string
	 */
	abstract protected function getConfigName();

	protected function setUp() {
		$this->config = $this->getConfigInstance();
		if (!$this->config->available()) {
			self::markTestSkipped(get_class($this->config) . ' is not available');
		}

		$this->resultFile = $this->files->get($this, '/config.result');
		$this->configFile = $this->files->get($this, '/config.' . $this->getConfigName());
		$this->routesFile = $this->files->get($this, '/routes.' . $this->getConfigName());
		if (file_exists($this->resultFile)) {
			@unLink($this->resultFile);
		}
	}

	public function testSavingConfiguration() {
		$data = include($this->files->get($this, '/config.source.php'));
		$this->config->write($data, $this->resultFile);
		self::assertFileExists($this->resultFile);
		self::assertFileEquals($this->configFile, $this->resultFile);
	}

	public function testReadingConfiguration() {
		$config   = $this->config->read($this->configFile);
		$expected = include($this->files->get($this, '/config.php'));
		self::assertEquals($expected, $config);
	}

	public function testSavingRoutes() {
		$routes = new \Nano\Routes();
		include($this->files->get($this, '/routes.source.php'));

		$this->config->writeRoutes($routes, $this->resultFile);

		self::assertFileExists($this->resultFile);
		self::assertEquals($routes, $this->config->readRoutes($this->resultFile));
	}

	public function testReadingRoutes() {
		$routes = new \Nano\Routes();
		include($this->files->get($this, '/routes.source.php'));

		$actual = $this->config->readRoutes($this->routesFile);
//		exit(var_export($actual, true));
		self::assertInstanceOf('\Nano\Routes', $actual);
		self::assertEquals($routes, $actual);
	}

	protected function tearDown() {
		if (file_exists($this->resultFile)) {
			@unLink($this->resultFile);
		}
		unSet($this->configFile, $this->resultFile, $this->routesFile, $this->config);
	}

}