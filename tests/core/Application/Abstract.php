<?php

abstract class Core_Application_Abstract extends TestUtils_TestCase {

	/**
	 * @var Application
	 */
	protected $application, $backup;

	/**
	 * @var string
	 */
	protected $workingDir;

	protected function setUp() {
		$this->backup = Nano::app();
		Nano::setApplication(null);

		$this->workingDir  = getCwd();
		chDir($GLOBALS['application']->rootDir);

		$this->application = new Application();
	}

	protected function tearDown() {
		Nano::setApplication(null);
		Nano::setApplication($this->backup);
		chDir($this->workingDir);
		unSet($this->workingDir, $this->application, $this->backup);
	}

}