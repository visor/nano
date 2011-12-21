<?php

/**
 * @group framework
 * @group config
 */
class Core_ConfigTest extends TestUtils_TestCase {

	/**
	 * @var Nano_Config_Format
	 */
	private $format = null;

	protected function setUp() {
		$this->format = self::getObjectProperty('Nano_Config', 'format');
		Nano_Config::setFormat(new Nano_Config_Format_Php());
	}

	public function testGetFormatShouldThrowExceptionWhenFormatNotSpecified() {
		self::setObjectProperty('Nano_Config', 'format', null);
		$this->setExpectedException('Nano_Config_Exception', 'Configuration: No configuration format specified');
		Nano_Config::getFormat();
	}

	public function testGetFormatThrowExceptionWhenFormatNotAvailable() {
		include_once $this->files->get($this, '/classes/Unsupported.php', '/Format');

		Nano_Config::setFormat(new Nano_Config_Format_Unsupported());
		$this->setExpectedException('Nano_Config_Exception', 'Configuration: Specified configuration format not available');
		Nano_Config::getFormat();
	}

	public function testGetDefaultFormatInstance() {
		self::assertInstanceOf('Nano_Config_Format', Nano_Config::getFormat());
		self::assertInstanceOf('Nano_Config_Format_Php', Nano_Config::getFormat());
	}

	public function testAfterCreatingConfigShouldBeEmpty() {
		$config = new Nano_Config($this->files->get($this, '/configs/default'));
		self::assertNull(self::getObjectProperty($config, 'config'));
	}

	public function testAfterSettingPathConfigShouldBeEmpty() {
		$config = new Nano_Config($this->files->get($this, '/configs/default'));
		$config->setPath($this->files->get($this, '/configs/empty.php'));
		self::assertNull(self::getObjectProperty($config, 'config'));
	}

	public function testGetPathShouldReturnPathPassedToConstructor() {
		$path   = $this->files->get($this, '/configs/default');
		$config = new Nano_Config($path);
		self::assertEquals($path, $config->getPath());
	}

	public function testGetPathShouldReturnPathPassedToSetPath() {
		$path   = $this->files->get($this, '/configs/default');
		$config = new Nano_Config('');
		$config->setPath($path);
		self::assertEquals($path, $config->getPath());
	}

	public function testExistsShouldReturnTrueForExistedProperties() {
		$config = new Nano_Config($this->files->get($this, '/configs/file1'));
		self::assertTrue($config->exists('param1'));
		self::assertTrue($config->exists('param2'));
	}

	public function testExistsShouldReturnFalseForNotExistedProperties() {
		$config = new Nano_Config($this->files->get($this, '/configs/file1'));
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

		$config = new Nano_Config(__FILE__);
		$config->set('test', 'value');
	}

	public function testGettingNotExistedPropertyShouldReturnNull() {
		$config = new Nano_Config($this->files->get($this, '/configs/file1'));
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
		$config   = new Nano_Config($this->files->get($this, '/configs/default'));
		$data     = $config->get('file1');
		$expected = (object)array('file1' => (object)array('param1' => 'value1'));
		$actual   = self::getObjectProperty($config, 'config');
		self::assertEquals($expected, $actual);
		self::assertEquals($expected->file1, $data);
	}

	public function testSettingConfigValueInRuntime() {
		$config = new Nano_Config($this->files->get($this, '/configs/default'));
		self::assertFalse($config->exists('section'));

		$config->set('section', 'value');
		self::assertTrue($config->exists('section'));
		self::assertEquals('value', $config->get('section'));
	}

	protected function tearDown() {
		if (null !== $this->format) {
			self::setObjectProperty('Nano_Config', 'format', $this->format);
		}
	}

}