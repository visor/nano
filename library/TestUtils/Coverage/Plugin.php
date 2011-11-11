<?php

class TestUtils_Coverage_Plugin implements Nano_C_Plugin {

	const PREPEND_FILE  = 'prepend.php';
	const APPEND_FILE   = 'append.php';
	const COVERAGE_FILE = 'coverage.php';
	const FILES_DIR     = 'files';
	const DATA_DIR      = 'coverage-data';

	/**
	 * @var ReflectionClass
	 */
	protected $seleniumDriver;

	/**
	 * @var string
	 */
	protected $rootDir, $dataDirName, $prependFileName, $appendFileName, $coverageFileName;

	public function __construct($rootDir) {
		$this->rootDir = $rootDir;
	}

	/**
	 * @return void
	 * @param Nano_C $controller
	 */
	public function init(Nano_C $controller) {
		$filesBaseDir           = __DIR__ . DIRECTORY_SEPARATOR . self::FILES_DIR;
		$this->dataDirName      = $this->rootDir . DIRECTORY_SEPARATOR . self::DATA_DIR;
		$this->prependFileName  = $filesBaseDir . DIRECTORY_SEPARATOR . self::PREPEND_FILE;
		$this->appendFileName   = $filesBaseDir . DIRECTORY_SEPARATOR . self::APPEND_FILE;
		$this->coverageFileName = $filesBaseDir . DIRECTORY_SEPARATOR . self::COVERAGE_FILE;

		$GLOBALS['PHPUNIT_COVERAGE_DATA_DIRECTORY'] = $this->dataDirName;

		if (!file_exists($this->dataDirName)) {
			mkDir($this->dataDirName, 0755, true);
		}

		if ($this->testIdExists()) {
			include $this->coverageFileName;
			exit();
		}
	}

	/**
	 * @return boolean
	 * @param Nano_C $controller
	 */
	public function before(Nano_C $controller) {
		if ($this->testCookieExists()) {
			include $this->prependFileName;
		}
	}

	/**
	 * @return void
	 * @param Nano_C $controller
	 */
	public function after(Nano_C $controller) {
		if ($this->testCookieExists()) {
			include $this->appendFileName;
		}
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

}