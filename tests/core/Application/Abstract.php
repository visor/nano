<?php

abstract class Core_Application_Abstract extends TestUtils_TestCase {

	/**
	 * @var Application
	 */
	protected $application;

	public static function setUpBeforeClass() {
		self::backupCurrentApplication();
	}

	protected function setUp() {
		self::setObjectProperty('Application', 'current', null);
		$this->application = new Application();
	}

	protected function tearDown() {
		self::setObjectProperty('Application', 'current', null);
		unSet($this->application);
	}

	public static function tearDownAfterClass() {
		self::restoreCurrentApplication();
	}

}