<?php

/**
 * @group core
 * @group config
 */
class Core_Config_Format_FactoryTest extends TestUtils_TestCase {

	public function testShouldReturnFormatInstanceIfAvailable() {
		self::assertInstanceOf('\Nano\Application\Config\Format', \Nano\Application\Config::format('php'));
		self::assertInstanceOf('\Nano\Application\Config\Format\Php', \Nano\Application\Config::format('php'));
	}

	public function testShouldThrowExceptionWhenFormatClassNotExists() {
		$this->setExpectedException('\Nano\Exception\UnsupportedConfigFormat', 'Unsupported format: not-supported');
		\Nano\Application\Config::format('not-supported');
	}

	public function testShouldThrowExceptionWhenFormatIsNotImplementsInterface() {
		$this->setExpectedException('\Nano\Exception\UnsupportedConfigFormat', 'Unsupported format: xml');

		include_once $this->files->get($this, '/classes/Xml.php');
		\Nano\Application\Config::format('xml');
	}

	public function testShouldThrowExceptionWhenFormatIsAbstract() {
		$this->setExpectedException('\Nano\Exception\UnsupportedConfigFormat', 'Unsupported format: abstract');

		include_once $this->files->get($this, '/classes/Abstractformat.php');
		\Nano\Application\Config::format('abstractformat');
	}

	public function testShouldThrowExceptionWhenFormatIsNotAvailable() {
		$this->setExpectedException('\Nano\Exception\UnsupportedConfigFormat', 'Unsupported format: unsupported');

		include_once $this->files->get($this, '/classes/Unsupported.php');
		\Nano\Application\Config::format('unsupported');
	}

}