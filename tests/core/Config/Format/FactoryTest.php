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
		self::assertException(
			function() {
				Nano_Config::formatFactory('not-supported');
			}
			, 'Nano_Exception_UnsupportedConfigFormat'
			, 'Unsupported format: not-supported'
		);
	}

	public function testShouldThrowExceptionWhenFormatIsNotImplementsInterface() {
		include_once $this->files->get($this, '/classes/Xml.php');
		self::assertException(
			function() {
				Nano_Config::formatFactory('xml');
			}
			, 'Nano_Exception_UnsupportedConfigFormat'
			, 'Unsupported format: xml'
		);
	}

	public function testShouldThrowExceptionWhenFormatIsAbstract() {
		include_once $this->files->get($this, '/classes/Abstract.php');
		self::assertException(
			function() {
				Nano_Config::formatFactory('abstract');
			}
			, 'Nano_Exception_UnsupportedConfigFormat'
			, 'Unsupported format: abstract'
		);
	}

	public function testShouldThrowExceptionWhenFormatIsNotAvailable() {
		include_once $this->files->get($this, '/classes/Unsupported.php');
		self::assertException(
			function() {
				Nano_Config::formatFactory('unsupported');
			}
			, 'Nano_Exception_UnsupportedConfigFormat'
			, 'Unsupported format: unsupported'
		);
	}

}