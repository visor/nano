<?php

abstract class Core_Config_Format_TestAbstract extends TestUtils_TestCase  {

	/**
	 * @var string
	 */
	protected $configFile, $resultFile;

	/**
	 * @var Nano_Config_Format
	 */
	protected $config;

	/**
	 * @return Nano_Config_Format
	 */
	abstract protected function getConfigInstance();

	/**
	 * @return string
	 */
	abstract protected function getConfigName();

	protected function setUp() {
		$this->config       = $this->getConfigInstance();
		if (!$this->config->available()) {
			self::markTestSkipped(get_class($this->config) . ' is not available');
		}

		$this->resultFile   = $this->files->get($this, '/config.result');
		$this->configFile   = $this->files->get($this, '/config.' . $this->getConfigName());
		if (file_exists($this->resultFile)) {
			@unlink($this->resultFile);
		}
	}

	public function testSavingConfiguration() {
		$data = include($this->files->get($this, '/config.source.php'));
		$this->config->write($data, $this->resultFile);
		self::assertFileEquals($this->configFile, $this->resultFile);
	}

	public function testReadingConfiguration() {
		$config   = $this->config->read($this->configFile);
		$expected = include($this->files->get($this, '/config.php'));
		self::assertEquals($expected, $config);
	}

	protected function tearDown() {
		$this->configFile = null;
		$this->config     = null;
		if (file_exists($this->resultFile)) {
			@unlink($this->resultFile);
		}
	}

}