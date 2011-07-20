<?php

/**
 * @group framework
 * @group config
 */
class Core_Config_BuilderTest extends TestUtils_TestCase {

	/**
	 * @var Nano_Config_Builder
	 */
	protected $builder;

	protected function setUp() {
		$this->files->clean($this, '/settings');
		$this->builder = new Nano_Config_Builder();
	}

	public function testDetectingFormatToSave() {
		self::assertInstanceOf('Nano_Config_Format_Php', $this->builder->detectFormat());

		$this->builder->addFormat(new Nano_Config_Format_Php());
		self::assertInstanceOf('Nano_Config_Format_Php', $this->builder->detectFormat());

		$this->builder->addFormat(new Nano_Config_Format_Serialize());
		self::assertInstanceOf('Nano_Config_Format_Serialize', $this->builder->detectFormat());

		if (function_exists('json_encode')) {
			$this->builder->addFormat(new Nano_Config_Format_Json());
			self::assertInstanceOf('Nano_Config_Format_Json', $this->builder->detectFormat());
		}

		if (function_exists('igbinary_unSerialize')) {
			$this->builder->addFormat(new Nano_Config_Format_Igbinary());
			self::assertInstanceOf('Nano_Config_Format_Igbinary', $this->builder->detectFormat());
		}
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

	public function testLoadingConfigurationWithChildOfChild() {
		$path = $this->files->get($this, '/settings/config.php');
		$this->builder->setSource($this->files->get($this, '/parents'));
		$this->builder->setDestination($path);
		$this->builder->build('child-of-child');

		self::assertFileExists($path);
		$config   = new Nano_Config($path);
		$expected = (object)array(
			  'file1' => (object)array('file1-param1' => 'new', 'param2' => (object)array('param2.1' => 'even-new-value2.1'))
			, 'file2' => (object)array('file2-param1' => 'value2', 'file2-param2' => 'value2', 'file2-param100' => '100')
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

	public function testDatabaseConfigurationBugFixed() {
		$path = $this->files->get($this, '/settings/config.php');
		$this->builder->setSource($this->files->get($this, '/database'));
		$this->builder->setDestination($path);
		$this->builder->build('child-of-child');
		self::assertFileExists($path);

		$config   = new Nano_Config($path);
		$expected = (object)array('db' => (object)array(
			'default' => (object)array(
				  'type'     => 'mysql'
				, 'dsn'      => 'host=localhost;dbname=bonus_hudson'
				, 'username' => 'user'
				, 'password' => ''
				, 'options'  => (object)array(
					PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
				)
				, 'log'      => APP . DS . 'sql.log'
			)
			, 'test' => (object)array(
				  'type'     => 'mysql'
				, 'dsn'      => 'host=localhost;dbname=bonus_hudson_test'
				, 'username' => 'user'
				, 'password' => ''
				, 'options'  => (object)array(
					PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
				)
				, 'log'      => APP . DS . 'test-sql.log'
			)
		));
		self::assertTrue($config->exists('db'));
		self::assertEquals($expected, self::getObjectProperty($config, 'config'));
	}

	protected function tearDown() {
		$this->files->clean($this, '/settings');
		$this->builder = null;
	}

}