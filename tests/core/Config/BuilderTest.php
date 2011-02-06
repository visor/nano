<?php

class Config_BuilderTest extends TestUtils_TestCase {

	/**
	 * @var Nano_Config_Builder
	 */
	protected $builder;

	protected function setUp() {
		$this->files->clean($this, '/settings');
		$this->builder = new Nano_Config_Builder();
	}

	public function testLoadingOnlyPhpFiles() {
		$this->builder->setSource($this->files->get($this, '/no-parents'));
		$this->builder->setDestination($this->files->get($this, '/settings/config.php'));
		$this->builder->build('default');
		self::assertFalse(in_array($this->files->get($this, '/no-parents/default/ignored.file'), get_included_files()));
	}

	public function testLoadingConfigurationWithoutParents() {
		$path = $this->files->get($this, '/settings/config.php');
		$this->builder->setSource($this->files->get($this, '/no-parents'));
		$this->builder->setDestination($path);
		$this->builder->build('default');

		self::assertFileExists($path);

		$config   = new Nano_Config($path);
		$expected = (object)array(
			  'file1' => (object)array('file1-param1' => 'value1')
			, 'file2' => (object)array('file2-param1' => 'value1')
		);

		self::assertTrue($config->exists('file1'));
		self::assertTrue($config->exists('file2'));
		self::assertEquals($expected, self::getObjectProperty($config, 'config'));
	}

	public function testLoadingConfigurationWithSingleParent() {
		$path = $this->files->get($this, '/settings/config.php');
		$this->builder->setSource($this->files->get($this, '/parents'));
		$this->builder->setDestination($path);
		$this->builder->build('basic-child');

		self::assertFileExists($path);
		$config   = new Nano_Config($path);
		$expected = (object)array(
			  'file1' => (object)array('file1-param1' => 'value1', 'param2' => (object)array('param2.1' => 'new-value2.1'))
			, 'file2' => (object)array('file2-param1' => 'new-value', 'file2-param2' => 'value2')
		);

		self::assertTrue($config->exists('file1'));
		self::assertTrue($config->exists('file2'));
		self::assertEquals($expected, self::getObjectProperty($config, 'config'));
	}

	public function testLoadingConfigurationWithSeveralParents() {
		$path = $this->files->get($this, '/settings/config.php');
		$this->builder->setSource($this->files->get($this, '/parents'));
		$this->builder->setDestination($path);
		$this->builder->build('ext-child');

		self::assertFileExists($path);
		$config   = new Nano_Config($path);
		$expected = (object)array(
			  'file1' => (object)array('file1-param1' => 'value1', 'param2' => (object)array('param2.1' => 'even-new-value2.1'))
			, 'file2' => (object)array('file2-param1' => 'value1', 'file2-param2' => 'value2', 'file2-param100' => '100')
		);

		self::assertTrue($config->exists('file1'));
		self::assertTrue($config->exists('file2'));
		self::assertEquals($expected, self::getObjectProperty($config, 'config'));
	}

	public function testLoadingConfigurationWithStdClasses() {
		$path = $this->files->get($this, '/settings/config.php');
		$this->builder->setSource($this->files->get($this, '/no-parents'));
		$this->builder->setDestination($path);
		$this->builder->build('objects');
		self::assertFileExists($path);

		$config   = new Nano_Config($path);
		$expected = (object)array(
			'db' => (object)array(
				'hostname' => 'localhost', 'username' => 'user', 'password' => 'p4ssw0rd', 'database' => 'db1'
			)
		);
		self::assertTrue($config->exists('db'));
		self::assertEquals($expected, self::getObjectProperty($config, 'config'));
	}

	protected function tearDown() {
		$this->files->clean($this, '/settings');
		$this->builder = null;
	}

}