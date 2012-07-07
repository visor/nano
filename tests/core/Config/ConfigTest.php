<?php

/**
 * @group core
 * @group config
 */
class Core_ConfigTest extends \Nano\TestUtils\TestCase {

	public function testNameShouldReturnNullWhenConfigurationNameNotSpecified() {
		$config = new \Nano\Application\Config($this->files->get($this, '/configs/empty-array'), new \Nano\Application\Config\Format\Php());
		self::assertNull($config->name());
	}

	public function testNameShouldReturnValueWhenPropertyStored() {
		$config = new \Nano\Application\Config($this->files->get($this, '/configs/named'), new \Nano\Application\Config\Format\Php());
		self::assertEquals('test', $config->name());
	}

	public function testLoadShouldUseEmptyStdClassWhenConfigIsEmpty() {
		$config = new \Nano\Application\Config($this->files->get($this, '/configs/empty-array'), new \Nano\Application\Config\Format\Php());
		self::assertNull($config->name());
		self::assertEquals(new stdClass, self::getObjectProperty($config, 'config'));
	}

	public function testGetFormatThrowExceptionWhenFormatNotAvailable() {
		include_once $this->files->get($this, '/classes/Unsupported.php', '/Format');

		$this->setExpectedException('\Nano\Exception\UnsupportedConfigFormat', 'Unsupported format: Nano\Application\Config\Format\Unsupported');
		new \Nano\Application\Config('', new \Nano\Application\Config\Format\Unsupported());
	}

	public function testAfterCreatingConfigShouldBeEmpty() {
		$config = new \Nano\Application\Config($this->files->get($this, '/configs/default'), new \Nano\Application\Config\Format\Php());
		self::assertNull(self::getObjectProperty($config, 'config'));
	}

	public function testGetPathShouldReturnPathPassedToConstructor() {
		$path   = $this->files->get($this, '/configs/default');
		$config = new \Nano\Application\Config($path, new \Nano\Application\Config\Format\Php());
		self::assertEquals($path, $config->getPath());
	}

	public function testExistsShouldReturnTrueForExistedProperties() {
		$config = new \Nano\Application\Config($this->files->get($this, '/configs/file1'), new \Nano\Application\Config\Format\Php());
		self::assertTrue($config->exists('param1'));
		self::assertTrue($config->exists('param2'));
	}

	public function testExistsShouldReturnFalseForNotExistedProperties() {
		$config = new \Nano\Application\Config($this->files->get($this, '/configs/file1'), new \Nano\Application\Config\Format\Php());
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

	public function testExistsShouldReturnFalseWhenConfigurationFileNotExists() {
		$config = new \Nano\Application\Config($this->files->get($this, '/configs/not-exists'), new \Nano\Application\Config\Format\Php());
		self::assertFalse($config->exists('some'));
	}

	public function testShouldTrowExceptionWhenNotExistedFileLoading() {
		$this->setExpectedException('Nano\Application\Config\Exception', 'Configuration files not exists at ' . __FILE__);

		$config = new \Nano\Application\Config(__FILE__, new \Nano\Application\Config\Format\Php());
		$config->set('test', 'value');
	}

	public function testGettingNotExistedPropertyShouldReturnNull() {
		$config = new \Nano\Application\Config($this->files->get($this, '/configs/file1'), new \Nano\Application\Config\Format\Php());
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
		$config   = new \Nano\Application\Config($this->files->get($this, '/configs/default'), new \Nano\Application\Config\Format\Php());
		$data     = $config->get('file1');
		$expected = (object)array('file1' => (object)array('param1' => 'value1'));
		$actual   = self::getObjectProperty($config, 'config');
		self::assertEquals($expected, $actual);
		self::assertEquals($expected->file1, $data);
	}

	public function testSettingConfigValueInRuntime() {
		$config = new \Nano\Application\Config($this->files->get($this, '/configs/default'), new \Nano\Application\Config\Format\Php());
		self::assertFalse($config->exists('section'));

		$config->set('section', 'value');
		self::assertTrue($config->exists('section'));
		self::assertEquals('value', $config->get('section'));
	}

	public function testGettingRoutes() {
		$config = new \Nano\Application\Config($this->files->get($this, '/configs/default'), new \Nano\Application\Config\Format\Php());
		self::assertInstanceOf('\Nano\Routes', $config->routes());
	}

	public function testConfigurationExistsShouldReturnFalseWhenNoConfigFileExists() {
		$config = new \Nano\Application\Config($this->files->get($this, '/configs/no-config'), new \Nano\Application\Config\Format\Php());

		self::assertFalse($config->configurationExists());
	}

	public function testExistsShouldReturnTrueWhenConfigurationFileExists() {
		$config = new \Nano\Application\Config($this->files->get($this, '/configs/no-routes'), new \Nano\Application\Config\Format\Php());

		self::assertTrue($config->configurationExists());
	}

	public function testConfigurationShouldBeLoadedWhenNoRoutes() {
		$config = new \Nano\Application\Config($this->files->get($this, '/configs/no-routes'), new \Nano\Application\Config\Format\Php());

		self::assertTrue($config->exists('file1'));
	}

	public function testRoutesShouldBeEmptyWhenNoRouteFileExists() {
		$config = new \Nano\Application\Config($this->files->get($this, '/configs/no-routes'), new \Nano\Application\Config\Format\Php());

		self::assertEquals(new \Nano\Routes(), $config->routes());
	}

}