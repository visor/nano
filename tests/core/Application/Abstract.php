<?php

abstract class Core_Application_Abstract extends \Nano\TestUtils\TestCase {

	/**
	 * @var \Nano\Application
	 */
	protected $application;

	/**
	 * @var string
	 */
	protected $workingDir;

	protected function setUp() {
		$this->app->backup();

		$this->workingDir  = getCwd();
		chDir($GLOBALS['application']->rootDir);

		$this->application = new \Nano\Application();
	}

	protected function tearDown() {
		chDir($this->workingDir);
		unSet($this->workingDir, $this->application);
		$this->app->restore();
	}

}