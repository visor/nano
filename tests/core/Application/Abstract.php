<?php

abstract class Core_Application_Abstract extends TestUtils_TestCase {

	/**
	 * @var Application
	 */
	protected $application;

	protected function setUp() {
		$this->application = new Application();
	}

	protected function tearDown() {
		unSet($this->application);
	}

}