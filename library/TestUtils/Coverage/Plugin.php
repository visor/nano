<?php

namespace Nano\TestUtils\Coverage;

class Plugin implements \Nano\Controller\Plugin {

	const DATA_DIR      = 'coverage-data';
	const FILES_DIR     = 'files';
	const PREPEND_FILE  = 'prepend.php';
	const APPEND_FILE   = 'append.php';
	const COVERAGE_FILE = 'coverage.php';

	/**
	 * @var string
	 */
	protected $rootDir, $dataDirName, $prependFileName, $appendFileName, $coverageFileName;

	public function __construct($rootDir) {
		$this->rootDir          = $rootDir;
		$this->dataDirName      = $this->rootDir . DS . self::DATA_DIR;

		$filesBaseDir           = __DIR__ . DS . self::FILES_DIR;
		$this->prependFileName  = $filesBaseDir . DS . self::PREPEND_FILE;
		$this->appendFileName   = $filesBaseDir . DS . self::APPEND_FILE;
		$this->coverageFileName = $filesBaseDir . DS . self::COVERAGE_FILE;

		$GLOBALS['PHPUNIT_COVERAGE_DATA_DIRECTORY'] = $this->dataDirName;

		if (!file_exists($this->dataDirName)) {
			mkDir($this->dataDirName, 0755, true);
		}

		if ($this->testIdExists()) {
			include $this->coverageFileName;
			exit();
		}

		if ($this->testCookieExists()) {
			include $this->prependFileName;
			register_shutdown_function(array($this, 'shutdown'));
		}
	}

	public function shutdown() {
		include $this->appendFileName;
	}

	/**
	 * @return boolean
	 */
	protected function testIdExists() {
		return isSet($_GET['PHPUNIT_SELENIUM_TEST_ID']);
	}

	/**
	 * @return boolean
	 */
	protected function testCookieExists() {
		return isSet($_COOKIE['PHPUNIT_SELENIUM_TEST_ID']);
	}

	/**
	 * @return void
	 * @param \Nano\Controller $controller
	 */
	public function init(\Nano\Controller $controller) {
	}

	/**
	 * @return boolean
	 * @param \Nano\Controller $controller
	 */
	public function before(\Nano\Controller $controller) {
	}

	/**
	 * @return void
	 * @param \Nano\Controller $controller
	 */
	public function after(\Nano\Controller $controller) {
	}

}