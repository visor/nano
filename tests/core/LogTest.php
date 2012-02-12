<?php

/**
 * @group core
 */
class Core_LogTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Application
	 */
	protected $application;

	/**
	 * @var Nano_Log
	 */
	protected $log;

	protected function setUp() {
		$this->application = new Application();
		$this->application->withRootDir(__DIR__ . DS . '_files');

		$this->log = new Nano_Log($this->application);
		$this->log->clear();
	}

	public function testClear() {
		self::assertFileNotExists($this->log->getFile());
	}

	public function testLog() {
		$this->log->message('some string');
		self::assertFileExists($this->log->getFile());
		self::assertEquals('some string' . PHP_EOL, file_get_contents($this->log->getFile()));
	}

	public function testGet() {
		$this->log->message('some string');
		self::assertFileExists($this->log->getFile());
		self::assertEquals('some string' . PHP_EOL, $this->log->get());

		$this->log->clear();
		self::assertEquals('', $this->log->get());
	}

	protected function tearDown() {
		$this->log->clear();
	}

}