<?php

/**
 * @group core
 * @group config
 */
class Core_Config_BuilderTest extends TestUtils_TestCase {

	/**
	 * @var Nano_Config_Builder
	 */
	protected $builder;

	/**
	 * @var Application
	 */
	protected $application;

	protected function setUp() {
		$this->application = new Application();
		$this->application
			->withConfigurationFormat('php')
			->withRootDir(__DIR__)
		;

		$this->files->clean($this, '/settings');
		$this->builder = new Nano_Config_Builder($this->application);
	}

	public function testSavingRoutes() {
		$routes = new Nano_Routes();
		include $this->files->get($this, '/parents/default/routes.php');
		include $this->files->get($this, '/parents/basic-child/routes.php');
		$expectedRoutes = $routes;
		unset($routes);

		$this->builder->setSource($this->files->get($this, '/parents'));
		$this->builder->setDestination($this->files->get($this, '/settings'));
		$this->builder->build('basic-child');

		self::assertFileExists($this->files->get($this, '/settings/routes'));
		$data = file_get_contents($this->files->get($this, '/settings/routes'));
		/** @var Nano_Routes $actualRoutes */
		$actualRoutes = unSerialize($data);
		self::assertEquals($expectedRoutes, $actualRoutes);
		self::assertTrue($actualRoutes->getRoutes('get')->offsetExists('/help'));
	}

	public function testAddingRoutesIntoChildConfigurations() {
		$routes = new Nano_Routes();
		include $this->files->get($this, '/parents/default/routes.php');
		include $this->files->get($this, '/parents/basic-child/routes.php');
		$expectedRoutes = $routes;
		unset($routes);

		$this->builder->setSource($this->files->get($this, '/parents'));
		$this->builder->setDestination($this->files->get($this, '/settings'));
		$this->builder->build('basic-child');

		self::assertFileExists($this->files->get($this, '/settings/routes'));
		$data = file_get_contents($this->files->get($this, '/settings/routes'));
		/** @var Nano_Routes $actualRoutes */
		$actualRoutes = unSerialize($data);
		self::assertEquals($expectedRoutes, $actualRoutes);
		self::assertTrue($actualRoutes->getRoutes('get')->offsetExists('/help'));
	}

	public function testOverrideRoutesIntoChildConfigurations() {
		$routes = new Nano_Routes();
		include $this->files->get($this, '/parents/default/routes.php');
		include $this->files->get($this, '/parents/basic-child/routes.php');
		include $this->files->get($this, '/parents/ext-child/routes.php');
		$expectedRoutes = $routes;
		unset($routes);

		$this->builder->setSource($this->files->get($this, '/parents'));
		$this->builder->setDestination($this->files->get($this, '/settings'));
		$this->builder->build('ext-child');

		self::assertFileExists($this->files->get($this, '/settings/routes'));
		$data = file_get_contents($this->files->get($this, '/settings/routes'));
		/** @var Nano_Routes $actualRoutes */
		$actualRoutes = unSerialize($data);
		self::assertEquals($expectedRoutes, $actualRoutes);
		self::assertTrue($actualRoutes->getRoutes('get')->offsetExists('/help'));
		self::assertTrue($actualRoutes->getRoutes('get')->offsetExists(''));
	}

	public function testLoadingOnlyPhpFiles() {
		$this->builder->setSource($this->files->get($this, '/no-parents'));
		$this->builder->setDestination($this->files->get($this, '/settings'));
		$this->builder->build('default');
		self::assertFalse(in_array($this->files->get($this, '/no-parents/default/ignored.file'), get_included_files()));
	}

	public function testLoadingConfigurationWithoutParents() {
		$path = $this->files->get($this, '/settings');
		$this->builder->setSource($this->files->get($this, '/no-parents'));
		$this->builder->setDestination($path);
		$this->builder->build('default');

		self::assertFileExists($path . DS . Nano_Config::CONFIG_FILE_NAME);

		$config   = new Nano_Config($path, $this->application->configFormat);
		$expected = (object)array(
			  'file1' => (object)array('file1-param1' => 'value1')
			, 'file2' => (object)array('file2-param1' => 'value1')
		);

		self::assertTrue($config->exists('file1'));
		self::assertTrue($config->exists('file2'));
		self::assertEquals($expected, self::getObjectProperty($config, 'config'));
	}

	public function testLoadingConfigurationWithSingleParent() {
		$path = $this->files->get($this, '/settings');
		$this->builder->setSource($this->files->get($this, '/parents'));
		$this->builder->setDestination($path);
		$this->builder->build('basic-child');

		self::assertFileExists($path . DS . Nano_Config::CONFIG_FILE_NAME);

		$config   = new Nano_Config($path, $this->application->configFormat);
		$config->get('file1');
		$expected = (object)array(
			  'file1' => (object)array('file1-param1' => 'value1', 'param2' => (object)array('param2.1' => 'new-value2.1'))
			, 'file2' => (object)array('file2-param1' => 'new-value', 'file2-param2' => 'value2')
		);

		self::assertTrue($config->exists('file1'));
		self::assertTrue($config->exists('file2'));
		self::assertEquals($expected, self::getObjectProperty($config, 'config'));
	}

	public function testLoadingConfigurationWithSeveralParents() {
		$path = $this->files->get($this, '/settings');
		$this->builder->setSource($this->files->get($this, '/parents'));
		$this->builder->setDestination($path);
		$this->builder->build('ext-child');

		self::assertFileExists($path . DS . Nano_Config::CONFIG_FILE_NAME);
		$config   = new Nano_Config($path, $this->application->configFormat);
		$expected = (object)array(
			  'file1' => (object)array('file1-param1' => 'value1', 'param2' => (object)array('param2.1' => 'even-new-value2.1'))
			, 'file2' => (object)array('file2-param1' => 'value1', 'file2-param2' => 'value2', 'file2-param100' => '100')
		);

		self::assertTrue($config->exists('file1'));
		self::assertTrue($config->exists('file2'));
		self::assertEquals($expected, self::getObjectProperty($config, 'config'));
	}

	public function testLoadingConfigurationWithChildOfChild() {
		$path = $this->files->get($this, '/settings');
		$this->builder->setSource($this->files->get($this, '/parents'));
		$this->builder->setDestination($path);
		$this->builder->build('child-of-child');

		self::assertFileExists($path . DS . Nano_Config::CONFIG_FILE_NAME);
		$config   = new Nano_Config($path, $this->application->configFormat);
		$expected = (object)array(
			  'file1' => (object)array('file1-param1' => 'new', 'param2' => (object)array('param2.1' => 'even-new-value2.1'))
			, 'file2' => (object)array('file2-param1' => 'value2', 'file2-param2' => 'value2', 'file2-param100' => '100')
		);

		self::assertTrue($config->exists('file1'));
		self::assertTrue($config->exists('file2'));
		self::assertEquals($expected, self::getObjectProperty($config, 'config'));
	}

	public function testLoadingConfigurationWithStdClasses() {
		$path = $this->files->get($this, '/settings');
		$this->builder->setSource($this->files->get($this, '/no-parents'));
		$this->builder->setDestination($path);
		$this->builder->build('objects');
		self::assertFileExists($path . DS . Nano_Config::CONFIG_FILE_NAME);

		$config   = new Nano_Config($path, $this->application->configFormat);
		$expected = (object)array(
			'db' => (object)array(
				'hostname' => 'localhost', 'username' => 'user', 'password' => 'p4ssw0rd', 'database' => 'db1'
			)
		);
		self::assertTrue($config->exists('db'));
		self::assertEquals($expected, self::getObjectProperty($config, 'config'));
	}

	public function testDatabaseConfigurationBugFixed() {
		$path = $this->files->get($this, '/settings');
		$this->builder->setSource($this->files->get($this, '/database'));
		$this->builder->setDestination($path);
		$this->builder->build('child-of-child');
		self::assertFileExists($path . DS . Nano_Config::CONFIG_FILE_NAME);

		$config   = new Nano_Config($path, $this->application->configFormat);
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

	public function testApplicationInstanceShoultBeAvailableInConfigSource() {
		$path = $this->files->get($this, '/settings');
		$this->builder->setSource($this->files->get($this, '/application'));
		$this->builder->setDestination($path);
		$this->builder->build('default');

		self::assertFileExists($path . DS . Nano_Config::CONFIG_FILE_NAME);
		self::assertFileExists($path . DS . Nano_Config::ROUTES_FILE_NAME);

		$config = new Nano_Config($path, $this->application->configFormat);

		self::assertTrue($config->exists('config'));
		self::assertInstanceOf('stdClass', $config->get('config'));
		self::assertObjectHasAttribute('root', $config->get('config'));
		self::assertEquals($this->application->rootDir, $config->get('config')->root);
	}

	protected function tearDown() {
		$this->files->clean($this, '/settings');
		unSet($this->builder, $this->application);
	}

}