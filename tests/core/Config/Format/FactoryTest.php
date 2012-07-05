<?php

/**
 * @group core
 * @group config
 */
class Core_Config_Format_FactoryTest extends TestUtils_TestCase {

	public function testShouldReturnFormatInstanceIfAvailable() {
		self::assertInstanceOf('Nano_Config_Format', Nano_Config::format('php'));
		self::assertInstanceOf('Nano_Config_Format_Php', Nano_Config::format('php'));
	}

	public function testShouldThrowExceptionWhenFormatClassNotExists() {
		$this->setExpectedException('\Nano\Exception\UnsupportedConfigFormat', 'Unsupported format: not-supported');
		Nano_Config::format('not-supported');
	}

	public function testShouldThrowExceptionWhenFormatIsNotImplementsInterface() {
		$this->setExpectedException('\Nano\Exception\UnsupportedConfigFormat', 'Unsupported format: xml');

		include_once $this->files->get($this, '/classes/Xml.php');
		Nano_Config::format('xml');
	}

	public function testShouldThrowExceptionWhenFormatIsAbstract() {
		$this->setExpectedException('\Nano\Exception\UnsupportedConfigFormat', 'Unsupported format: abstract');

		include_once $this->files->get($this, '/classes/Abstract.php');
		Nano_Config::format('abstract');
	}

	public function testShouldThrowExceptionWhenFormatIsNotAvailable() {
		$this->setExpectedException('\Nano\Exception\UnsupportedConfigFormat', 'Unsupported format: unsupported');

		include_once $this->files->get($this, '/classes/Unsupported.php');
		Nano_Config::format('unsupported');
	}

}