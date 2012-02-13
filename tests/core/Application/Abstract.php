<?php

abstract class Core_Application_Abstract extends TestUtils_TestCase {

	/**
	 * @var Application
	 */
	protected $application;

	/**
	 * @var string
	 */
	protected $workingDir;

	protected function setUp() {
		$this->workingDir  = getCwd();
		chDir($GLOBALS['application']->rootDir);

		$this->application = new Application();
	}

	protected function tearDown() {
		chDir($this->workingDir);
		unSet($this->workingDir, $this->application);
	}

}