<?php

/**
 * @group framework
 * @group config
 */
class Nano_ConfigTest extends TestUtils_TestCase {

	public function testAfterCreatingConfigShouldBeEmpty() {
		$config = new Nano_Config($this->files->get($this, '/configs/default.php'));
		self::assertNull(self::getObjectProperty($config, 'config'));
	}

	public function testAfterSettingPathConfigShouldBeEmpty() {
		$config = new Nano_Config($this->files->get($this, '/configs/default.php'));
		$config->setPath($this->files->get($this, '/configs/empty.php'));
		self::assertNull(self::getObjectProperty($config, 'config'));
	}

	public function testGetPathShouldReturnPathPassedToConstructor() {
		$path   = $this->files->get($this, '/configs/default.php');
		$config = new Nano_Config($path);
		self::assertEquals($path, $config->getPath());
	}

	public function testGetPathShouldReturnPathPassedToSetPath() {
		$path   = $this->files->get($this, '/configs/default.php');
		$config = new Nano_Config('');
		$config->setPath($path);
		self::assertEquals($path, $config->getPath());
	}

	public function testExistsShouldReturnTrueForExistedProperties() {
		$config = new Nano_Config($this->files->get($this, '/configs/file1.php'));
		self::assertTrue($config->exists('param1'));
		self::assertTrue($config->exists('param2'));
	}

	public function testExistsShouldReturnFalseForNotExistedProperties() {
		$config = new Nano_Config($this->files->get($this, '/configs/file1.php'));
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
		self::assertException(
			function() {
				$config = new Nano_Config(__FILE__ . '.php');
				$config->get(null);
			}
			, 'Nano_Exception'
			, 'File "' . __FILE__ . '.php" not found'
		);
	}

	public function testShouldTrowExceptionWhenNotReadableFileLoading() {
		$file = $this->files->get($this, '/configs/not-readable.php');
		if (!file_exists($file)) {
			self::markTestSkipped('Notreadable file not exists');
		}
		chMod($file, 0);
		self::assertException(
			function() use ($file) {
				$config = new Nano_Config($file);
				$config->get(null);
			}
			, 'Nano_Exception'
			, 'Cannot read file "' . $file . '"'
		);
	}

	public function testGettingNotExistedPropertyShouldReturnNull() {
		$config = new Nano_Config($this->files->get($this, '/configs/file1.php'));
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
		$config   = new Nano_Config($this->files->get($this, '/configs/default.php'));
		$data     = $config->get('file1');
		$expected = (object)array('file1' => (object)array('param1' => 'value1'));
		$actual   = self::getObjectProperty($config, 'config');
		self::assertEquals($expected, $actual);
		self::assertEquals($expected->file1, $data);
	}

	public function testSettingConfigValueInRuntime() {
		$config = new Nano_Config($this->files->get($this, '/configs/default.php'));
		self::assertFalse($config->exists('section'));

		$config->set('section', 'value');
		self::assertTrue($config->exists('section'));
		self::assertEquals('value', $config->get('section'));
	}

	protected function tearDown() {
		parent::tearDown();
	}

}