<?php

class LogTest extends PHPUnit_Framework_TestCase {

	protected function setUp() {
		Nano_Log::clear();
	}

	public function testClear() {
		Nano_Log::clear();
		self::assertFileNotExists(Nano_Log::getFile());
	}

	public function testLog() {
		Nano_Log::message('some string');
		self::assertFileExists(Nano_Log::getFile());
		self::assertEquals('some string' . PHP_EOL, file_get_contents(Nano_Log::getFile()));
	}

	public function testGet() {
		Nano_Log::message('some string');
		self::assertFileExists(Nano_Log::getFile());
		self::assertEquals('some string' . PHP_EOL, Nano_Log::get());

		Nano_Log::clear();
		self::assertEquals('', Nano_Log::get());
	}

	protected function tearDown() {
		Nano_Log::clear();
	}

}