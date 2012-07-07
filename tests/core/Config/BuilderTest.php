<?php

/**
 * @group core
 * @group config
 */
class Core_Config_BuilderTest extends \Nano\TestUtils\TestCase {

	/**
	 * @var \Nano\Application\Config\Builder
	 */
	protected $builder;

	/**
	 * @var \Nano\Application
	 */
	protected $application;

	protected function setUp() {
		$this->app->backup();
		$this->application = new \Nano\Application();
		$this->application
			->withConfigurationFormat('php')
			->withRootDir(__DIR__)
		;

		$this->files->clean($this, '/settings');
		$this->builder = new \Nano\Application\Config\Builder($this->application);
	}

	public function testSavingRoutes() {
		$routes = new \Nano\Routes();
		include $this->files->get($this, '/parents/default/routes.php');
		include $this->files->get($this, '/parents/basic-child/routes.php');
		$expectedRoutes = $routes;
		unset($routes);

		$this->builder->setSource($this->files->get($this, '/parents'));
		$this->builder->setDestination($this->files->get($this, '/settings'));
		$this->builder->build('basic-child');

		self::assertFileExists($this->files->get($this, '/settings/routes'));
		$data = file_get_contents($this->files->get($this, '/settings/routes'));
		/** @var \Nano\Routes $actualRoutes */
		$actualRoutes = unSerialize($data);
		self::assertEquals($expectedRoutes, $actualRoutes);
	}

	public function testAddingRoutesIntoChildConfigurations() {
		$routes = new \Nano\Routes();
		include $this->files->get($this, '/parents/default/routes.php');
		include $this->files->get($this, '/parents/basic-child/routes.php');
		$expectedRoutes = $routes;
		unset($routes);

		$this->builder->setSource($this->files->get($this, '/parents'));
		$this->builder->setDestination($this->files->get($this, '/settings'));
		$this->builder->build('basic-child');

		self::assertFileExists($this->files->get($this, '/settings/routes'));
		$data = file_get_contents($this->files->get($this, '/settings/routes'));
		/** @var \Nano\Routes $actualRoutes */
		$actualRoutes = unSerialize($data);
		self::assertEquals($expectedRoutes, $actualRoutes);
	}

	public function testOverrideRoutesIntoChildConfigurations() {
		$routes = new \Nano\Routes();
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
		/** @var \Nano\Routes $actualRoutes */
		$actualRoutes = unSerialize($data);
		self::assertEquals($expectedRoutes, $actualRoutes);
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

		self::assertFileExists($path . DS . \Nano\Application\Config::CONFIG_FILE_NAME);

		$config   = new \Nano\Application\Config($path, $this->application->configFormat);
		$expected = (object)array(
			  'file1'  => (object)array('file1-param1' => 'value1')
			, 'file2'  => (object)array('file2-param1' => 'value1')
			, '__name' => 'default'
		);

		self::assertTrue($config->exists('file1'));
		self::assertTrue($config->exists('file2'));
		self::assertEquals('default', $config->name());
		self::assertEquals($expected, self::getObjectProperty($config, 'config'));
	}

	public function testLoadingConfigurationWithSingleParent() {
		$path = $this->files->get($this, '/settings');
		$this->builder->setSource($this->files->get($this, '/parents'));
		$this->builder->setDestination($path);
		$this->builder->build('basic-child');

		self::assertFileExists($path . DS . \Nano\Application\Config::CONFIG_FILE_NAME);

		$config   = new \Nano\Application\Config($path, $this->application->configFormat);
		$config->get('file1');
		$expected = (object)array(
			  'file1'  => (object)array('file1-param1' => 'value1', 'param2' => (object)array('param2.1' => 'new-value2.1'))
			, 'file2'  => (object)array('file2-param1' => 'new-value', 'file2-param2' => 'value2')
			, '__name' => 'basic-child'
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

		self::assertFileExists($path . DS . \Nano\Application\Config::CONFIG_FILE_NAME);
		$config   = new \Nano\Application\Config($path, $this->application->configFormat);
		$expected = (object)array(
			  'file1'  => (object)array('file1-param1' => 'value1', 'param2' => (object)array('param2.1' => 'even-new-value2.1'))
			, 'file2'  => (object)array('file2-param1' => 'value1', 'file2-param2' => 'value2', 'file2-param100' => '100')
			, '__name' => 'ext-child'
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

		self::assertFileExists($path . DS . \Nano\Application\Config::CONFIG_FILE_NAME);
		$config   = new \Nano\Application\Config($path, $this->application->configFormat);
		$expected = (object)array(
			  'file1'  => (object)array('file1-param1' => 'new', 'param2' => (object)array('param2.1' => 'even-new-value2.1'))
			, 'file2'  => (object)array('file2-param1' => 'value2', 'file2-param2' => 'value2', 'file2-param100' => '100')
			, '__name' => 'child-of-child'
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
		self::assertFileExists($path . DS . \Nano\Application\Config::CONFIG_FILE_NAME);

		$config   = new \Nano\Application\Config($path, $this->application->configFormat);
		$expected = (object)array(
			'db' => (object)array(
				'hostname' => 'localhost', 'username' => 'user', 'password' => 'p4ssw0rd', 'database' => 'db1'
			)
			, '__name' => 'objects'
		);
		self::assertTrue($config->exists('db'));
		self::assertEquals($expected, self::getObjectProperty($config, 'config'));
	}

	public function testDatabaseConfigurationBugFixed() {
		$path = $this->files->get($this, '/settings');
		$this->builder->setSource($this->files->get($this, '/database'));
		$this->builder->setDestination($path);
		$this->builder->build('child-of-child');
		self::assertFileExists($path . DS . \Nano\Application\Config::CONFIG_FILE_NAME);

		$config   = new \Nano\Application\Config($path, $this->application->configFormat);
		$expected = (object)array(
			'db' => (object)array(
				'default' => (object)array(
					  'type'     => 'mysql'
					, 'dsn'      => 'host=localhost;dbname=bonus_hudson'
					, 'username' => 'user'
					, 'password' => ''
					, 'options'  => (object)array(
						PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
					)
					, 'log'      => $this->application->rootDir . '/sql.log'
				)
				, 'test' => (object)array(
					  'type'     => 'mysql'
					, 'dsn'      => 'host=localhost;dbname=bonus_hudson_test'
					, 'username' => 'user'
					, 'password' => ''
					, 'options'  => (object)array(
						PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
					)
					, 'log'      => $this->application->rootDir . '/test-sql.log'
				)
			)
			, '__name' => 'child-of-child'
		);
		self::assertTrue($config->exists('db'));
		self::assertEquals($expected, self::getObjectProperty($config, 'config'));
	}

	public function testApplicationInstanceShoultBeAvailableInConfigSource() {
		$path = $this->files->get($this, '/settings');
		$this->builder->setSource($this->files->get($this, '/application'));
		$this->builder->setDestination($path);
		$this->builder->build('default');

		self::assertFileExists($path . DS . \Nano\Application\Config::CONFIG_FILE_NAME);
		self::assertFileExists($path . DS . \Nano\Application\Config::ROUTES_FILE_NAME);

		$config = new \Nano\Application\Config($path, $this->application->configFormat);

		self::assertTrue($config->exists('config'));
		self::assertInstanceOf('stdClass', $config->get('config'));
		self::assertObjectHasAttribute('root', $config->get('config'));
		self::assertEquals($this->application->rootDir, $config->get('config')->root);
	}

	public function testClearShoultReturnFalseWhenNoDestination() {
		self::assertFalse($this->builder->clean());
	}

	public function testClearingDestination() {
		$path = $this->files->get($this, '/settings');
		$this->builder->setSource($this->files->get($this, '/application'));
		$this->builder->setDestination($path);
		$this->builder->build('default');

		self::assertFileExists($path . DS . \Nano\Application\Config::CONFIG_FILE_NAME);
		self::assertFileExists($path . DS . \Nano\Application\Config::ROUTES_FILE_NAME);

		self::assertTrue($this->builder->clean());
		self::assertFileNotExists($path . DS . \Nano\Application\Config::CONFIG_FILE_NAME);
		self::assertFileNotExists($path . DS . \Nano\Application\Config::ROUTES_FILE_NAME);
	}

	protected function tearDown() {
		$this->files->clean($this, '/settings');
		unSet($this->builder, $this->application);
		$this->app->restore();
	}

}