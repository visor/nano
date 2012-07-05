<?php

/**
 * @group core
 * @group names
 */
class Core_NamesTest extends TestUtils_TestCase {

	protected function setUp() {
		$this->app->backup();
		Nano::setApplication($GLOBALS['application']);

		Nano::app()->withModule('some',  $this->files->get($this, '/module1'));
		Nano::app()->withModule('other', $this->files->get($this, '/module2'));
	}

	public function testConvertNanoClassToFile() {
		$root = Nano::app()->nanoRootDir . '/library';
		self::assertEquals($root . DS . 'Loader.php', \Nano\Names::nanoFile('\Nano\\Loader'));
		self::assertEquals($root . DS . 'Event' . DS . 'Manager.php', \Nano\Names::nanoFile('\Nano\\Event\\Manager'));
	}

	public function testConvertAppControllerClassToFile() {
		$root = Nano::app()->rootDir . '/controllers';
		self::assertEquals($root . DS . 'Some.php', \Nano\Names::applicationFile('\\App\\Controller\\Some'));
		self::assertEquals($root . DS . 'Some' . DS . 'Other.php', \Nano\Names::applicationFile('\\App\\Controller\\Some\\Other'));
	}

	public function testConvertAppHelperClassToFile() {
		$root = Nano::app()->rootDir . '/helpers';
		self::assertEquals($root . DS . 'Some.php', \Nano\Names::applicationFile('\\App\\Helper\\Some'));
		self::assertEquals($root . DS . 'Some' . DS . 'Other.php', \Nano\Names::applicationFile('\\App\\Helper\\Some\\Other'));
	}

	public function testConvertAppLibraryClassToFile() {
		$root = Nano::app()->rootDir . '/library';
		self::assertEquals($root . DS . 'Some.php',       \Nano\Names::applicationFile('\\App\\Some'));
		self::assertEquals($root . DS . 'Helper.php',     \Nano\Names::applicationFile('\\App\\Helper'));
		self::assertEquals($root . DS . 'Controller.php', \Nano\Names::applicationFile('\\App\\Controller'));
		self::assertEquals($root . DS . 'Plugin.php',     \Nano\Names::applicationFile('\\App\\Plugin'));
		self::assertEquals($root . DS . 'Model.php',      \Nano\Names::applicationFile('\\App\\Model'));
	}

	public function testConvertAppModelClassToFile() {
		$root = Nano::app()->rootDir . '/models';
		self::assertEquals($root . DS . 'Some.php', \Nano\Names::applicationFile('\\App\\Model\\Some'));
		self::assertEquals($root . DS . 'Some' . DS . 'Other.php', \Nano\Names::applicationFile('\\App\\Model\\Some\\Other'));
	}

	public function testConvertAppPluginsClassToFile() {
		$root = Nano::app()->rootDir . '/plugins';
		self::assertEquals($root . DS . 'Some.php', \Nano\Names::applicationFile('\\App\\Plugin\\Some'));
		self::assertEquals($root . DS . 'Some' . DS . 'Other.php', \Nano\Names::applicationFile('\\App\\Plugin\\Some\\Other'));
	}

	public function testConvertModuleControllerClassToFile() {
		$someRoot  = Nano::app()->modules->getPath('some', 'controllers');
		$otherRoot = Nano::app()->modules->getPath('other', 'controllers');
		self::assertEquals($someRoot . DS . 'Index.php',                \Nano\Names::moduleFile('\\Module\\Some\\Controller\\Index'));
		self::assertEquals($someRoot . DS . 'News' . DS . 'Index.php',  \Nano\Names::moduleFile('\\Module\\Some\\Controller\\News\\Index'));
		self::assertEquals($otherRoot . DS . 'Index.php',               \Nano\Names::moduleFile('\\Module\\Other\\Controller\\Index'));
		self::assertEquals($otherRoot . DS . 'News' . DS . 'Index.php', \Nano\Names::moduleFile('\\Module\\Other\\Controller\\News\\Index'));
	}

	public function testConvertModuleHelperClassToFile() {
		$someRoot  = Nano::app()->modules->getPath('some', 'helpers');
		$otherRoot = Nano::app()->modules->getPath('other', 'helpers');
		self::assertEquals($someRoot . DS . 'Foo.php',  \Nano\Names::moduleFile('\\Module\\Some\\Helper\\Foo'));
		self::assertEquals($otherRoot . DS . 'Foo.php', \Nano\Names::moduleFile('\\Module\\Other\\Helper\\Foo'));
	}

	public function testConvertModuleLibraryClassToFile() {
		$someRoot  = Nano::app()->modules->getPath('some', 'library');
		$otherRoot = Nano::app()->modules->getPath('other', 'library');
		self::assertEquals($someRoot . DS . 'Foo.php',         \Nano\Names::moduleFile('\\Module\\Some\\Foo'));
		self::assertEquals($someRoot . DS . 'Controller.php',  \Nano\Names::moduleFile('\\Module\\Some\\Controller'));
		self::assertEquals($someRoot . DS . 'Helper.php',      \Nano\Names::moduleFile('\\Module\\Some\\Helper'));
		self::assertEquals($someRoot . DS . 'Model.php',       \Nano\Names::moduleFile('\\Module\\Some\\Model'));
		self::assertEquals($someRoot . DS . 'Plugin.php',      \Nano\Names::moduleFile('\\Module\\Some\\Plugin'));
		self::assertEquals($otherRoot . DS . 'Foo.php',        \Nano\Names::moduleFile('\\Module\\Other\\Foo'));
		self::assertEquals($otherRoot . DS . 'Controller.php', \Nano\Names::moduleFile('\\Module\\Other\\Controller'));
		self::assertEquals($otherRoot . DS . 'Helper.php',     \Nano\Names::moduleFile('\\Module\\Other\\Helper'));
		self::assertEquals($otherRoot . DS . 'Model.php',      \Nano\Names::moduleFile('\\Module\\Other\\Model'));
		self::assertEquals($otherRoot . DS . 'Plugin.php',     \Nano\Names::moduleFile('\\Module\\Other\\Plugin'));
	}

	public function testConvertModuleModelClassToFile() {
		$someRoot  = Nano::app()->modules->getPath('some', 'models');
		$otherRoot = Nano::app()->modules->getPath('other', 'models');
		self::assertEquals($someRoot . DS . 'Foo.php',  \Nano\Names::moduleFile('\\Module\\Some\\Model\\Foo'));
		self::assertEquals($otherRoot . DS . 'Foo.php', \Nano\Names::moduleFile('\\Module\\Other\\Model\\Foo'));
	}

	public function testConvertModulePluginsClassToFile() {
		$someRoot  = Nano::app()->modules->getPath('some', 'plugins');
		$otherRoot = Nano::app()->modules->getPath('other', 'plugins');
		self::assertEquals($someRoot . DS . 'Foo.php',  \Nano\Names::moduleFile('\\Module\\Some\\Plugin\\Foo'));
		self::assertEquals($otherRoot . DS . 'Foo.php', \Nano\Names::moduleFile('\\Module\\Other\\Plugin\\Foo'));
	}

	public function testShouldThrowExceptionWhenModuleNotFound() {
		$this->setExpectedException('Application_Exception_ModuleNotFound', 'Module \'test\' not found');
		\Nano\Names::moduleFile('\\Module\\Test\\Plugin\\Foo');
	}

	public function testAddingApplicationNamespaceForControllerClass() {
		self::assertEquals('App\\Controller\\Some',       \Nano\Names::controllerClass('some'));
		self::assertEquals('App\\Controller\\YetAnother', \Nano\Names::controllerClass('yet-another'));
	}

	public function testUsingControllerAdditionalNamespace() {
		self::assertEquals('App\\Controller\\News\\Index', \Nano\Names::controllerClass('news/index'));
	}

	public function testAddingModuleNamespaceForControllerClass() {
		self::assertEquals('Module\\Test\\Controller\\Index', \Nano\Names::controllerClass('index', 'test'));
	}

	public function testUsingControllerAdditionalNamespaceWithModuleNamespace() {
		self::assertEquals('Module\\Test\\Controller\\News\\Backend', \Nano\Names::controllerClass('news/backend', 'test'));
	}

	public function testAddingApplicationNamespaceForHelperClass() {
		self::assertEquals('App\\Helper\\Some',       \Nano\Names::helperClass('some'));
		self::assertEquals('App\\Helper\\YetAnother', \Nano\Names::helperClass('yet-another'));
	}

	public function testUsingHelperAdditionalNamespace() {
		self::assertEquals('App\\Helper\\News\\Index', \Nano\Names::helperClass('news/index'));
	}

	public function testAddingModuleNamespaceForHelperClass() {
		self::assertEquals('Module\\Test\\Helper\\Index', \Nano\Names::helperClass('index', 'test'));
	}

	public function testUsingHelperAdditionalNamespaceWithModuleNamespace() {
		self::assertEquals('Module\\Test\\Helper\\News\\Backend', \Nano\Names::helperClass('news/backend', 'test'));
	}

	protected function tearDown() {
		$this->app->restore();
	}

}