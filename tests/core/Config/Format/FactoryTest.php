<?php

/**
 * @group framework
 * @group config
 */
class Core_Config_Format_FactoryTest extends TestUtils_TestCase {

	public function testShouldReturnFormatInstanceIfAvailable() {
		self::assertInstanceOf('Nano_Config_Format', Nano_Config::formatFactory('php'));
		self::assertInstanceOf('Nano_Config_Format_Php', Nano_Config::formatFactory('php'));
	}

	public function testShouldThrowExceptionWhenFormatClassNotExists() {
		$this->setExpectedException('Nano_Exception_UnsupportedConfigFormat', 'Unsupported format: not-supported');
		Nano_Config::formatFactory('not-supported');
	}

	public function testShouldThrowExceptionWhenFormatIsNotImplementsInterface() {
		$this->setExpectedException('Nano_Exception_UnsupportedConfigFormat', 'Unsupported format: xml');

		include_once $this->files->get($this, '/classes/Xml.php');
		Nano_Config::formatFactory('xml');
	}

	public function testShouldThrowExceptionWhenFormatIsAbstract() {
		$this->setExpectedException('Nano_Exception_UnsupportedConfigFormat', 'Unsupported format: abstract');

		include_once $this->files->get($this, '/classes/Abstract.php');
		Nano_Config::formatFactory('abstract');
	}

	public function testShouldThrowExceptionWhenFormatIsNotAvailable() {
		$this->setExpectedException('Nano_Exception_UnsupportedConfigFormat', 'Unsupported format: unsupported');

		include_once $this->files->get($this, '/classes/Unsupported.php');
		Nano_Config::formatFactory('unsupported');
	}

}