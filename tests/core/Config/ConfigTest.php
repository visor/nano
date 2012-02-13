<?php

/**
 * @group core
 * @group config
 */
class Core_ConfigTest extends TestUtils_TestCase {

	public function testGetFormatThrowExceptionWhenFormatNotAvailable() {
		include_once $this->files->get($this, '/classes/Unsupported.php', '/Format');

		$this->setExpectedException('Nano_Exception_UnsupportedConfigFormat', 'Unsupported format: Nano_Config_Format_Unsupported');
		new Nano_Config('', new Nano_Config_Format_Unsupported());
	}

	public function testAfterCreatingConfigShouldBeEmpty() {
		$config = new Nano_Config($this->files->get($this, '/configs/default'), new Nano_Config_Format_Php());
		self::assertNull(self::getObjectProperty($config, 'config'));
	}

	public function testGetPathShouldReturnPathPassedToConstructor() {
		$path   = $this->files->get($this, '/configs/default');
		$config = new Nano_Config($path, new Nano_Config_Format_Php());
		self::assertEquals($path, $config->getPath());
	}

	public function testExistsShouldReturnTrueForExistedProperties() {
		$config = new Nano_Config($this->files->get($this, '/configs/file1'), new Nano_Config_Format_Php());
		self::assertTrue($config->exists('param1'));
		self::assertTrue($config->exists('param2'));
	}

	public function testExistsShouldReturnFalseForNotExistedProperties() {
		$config = new Nano_Config($this->files->get($this, '/configs/file1'), new Nano_Config_Format_Php());
		$names  = array(
			'param1-not-exists'
			, 'param2-not-exists'
			, null
			, true
			, false
			, ''
		);
		foreach ($names as $name) {
			self::assertFalse($config->exists($name));
		}
	}

	public function testShouldTrowExceptionWhenNotExistedFileLoading() {
		$this->setExpectedException('Nano_Config_Exception', 'Configuration files not exists at ' . __FILE__);

		$config = new Nano_Config(__FILE__, new Nano_Config_Format_Php());
		$config->set('test', 'value');
	}

	public function testGettingNotExistedPropertyShouldReturnNull() {
		$config = new Nano_Config($this->files->get($this, '/configs/file1'), new Nano_Config_Format_Php());
		$names  = array(
			'param1-not-exists'
			, 'param2-not-exists'
			, null
			, true
			, false
			, ''
		);
		foreach ($names as $name) {
			self::assertNull($config->get($name));
		}
	}

	public function testGettingWholeFile() {
		$config   = new Nano_Config($this->files->get($this, '/configs/default'), new Nano_Config_Format_Php());
		$data     = $config->get('file1');
		$expected = (object)array('file1' => (object)array('param1' => 'value1'));
		$actual   = self::getObjectProperty($config, 'config');
		self::assertEquals($expected, $actual);
		self::assertEquals($expected->file1, $data);
	}

	public function testSettingConfigValueInRuntime() {
		$config = new Nano_Config($this->files->get($this, '/configs/default'), new Nano_Config_Format_Php());
		self::assertFalse($config->exists('section'));

		$config->set('section', 'value');
		self::assertTrue($config->exists('section'));
		self::assertEquals('value', $config->get('section'));
	}

}