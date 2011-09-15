<?php

abstract class Core_Application_Abstract extends TestUtils_TestCase {

	/**
	 * @var Application
	 */
	protected $application;

	protected function setUp() {
		self::setObjectProperty('Application', 'current', null);
		$this->application = new Application();
	}

	protected function tearDown() {
		self::setObjectProperty('Application', 'current', null);
		unSet($this->application);
	}

}