<?php

require_once __DIR__ . '/Abstract.php';

/**
 * @group core
 */
class Core_Application_LoaderTest extends Core_Application_Abstract {

	protected $includePath = '';

	protected function setUp() {
		parent::setUp();

		$this->includePath = get_include_path();
	}

	public function testFormatingModuleName() {
		self::assertEquals('Test_Module\\LibraryClass',        Nano_Loader::formatModuleClassName('test', 'library-class'));
		self::assertEquals('Test_Module\\ClassController',     Nano_Loader::formatModuleClassName('test', 'class-controller'));
		self::assertEquals('Test_Module\\SomeClassController', Nano_Loader::formatModuleClassName('test', 'some-class-controller'));
	}

	public function testExtractginModuleAndClassName() {
		self::assertEquals(array('Test_Module', 'LibraryClass'),    Nano_Loader::extractModuleClassParts('Test_Module\\LibraryClass'));
		self::assertEquals(array('Test_Module', 'ControllerClass'), Nano_Loader::extractModuleClassParts('Test_Module\\ControllerClass'));
		self::assertEquals(array('Test_Module', 'ModelClass'),      Nano_Loader::extractModuleClassParts('Test_Module\\ModelClass'));
		self::assertEquals(array('Test_Module', 'PluginClass'),     Nano_Loader::extractModuleClassParts('Test_Module\\PluginClass'));
	}

	public function testDetectingModuleClass() {
		self::assertFalse(Nano_Loader::isModuleClass(__CLASS__));
		self::assertFalse(Nano_Loader::isModuleClass('M\\ClassName'));
		self::assertFalse(Nano_Loader::isModuleClass('M\\ModuleName_'));
		self::assertTrue(Nano_Loader::isModuleClass('SomeName_Module\\ClassName'));
	}

	public function testLoaderShouldInitializedWhenApplicationCreated() {
		self::assertInstanceOf('Nano_Loader', $this->application->loader);
	}

	public function testNanoClassShouldBeLoaded() {
		self::assertTrue(class_exists('Nano', false));
	}

	public function testLoadingCoreClass() {
		$this->assertClassLoaded('TestUtils_Example');
	}

	public function testLoadingApplicationLibraryClass() {
		$this->application->withRootDir($this->files->get($this, ''));

		$this->assertClassLoaded('TestApplicationClassController');
		$this->assertClassLoaded('TestApplicationPluginClass');
		$this->assertClassLoaded('TestApplicationModelClass');
		$this->assertClassLoaded('TestApplicationLibraryClass');
	}

	public function testLoadingModuleClasses() {
		$this->application->withModule('test', $this->files->get($this, '/test'));

		$this->assertClassLoaded('Test_Module\\Class2Controller');
		$this->assertClassLoaded('Test_Module\\Library2Class');
		$this->assertClassLoaded('Test_Module\\Model2Class');
		$this->assertClassLoaded('Test_Module\\Plugin2Class');
	}

	public function testShouldReturnTrueWhenClassAlreadyLoaded() {
		$this->application->withRootDir($this->files->get($this, ''));
		self::assertTrue($this->application->loader->loadClass('DoubleLoaded'));
		self::assertTrue($this->application->loader->loadClass('DoubleLoaded'));
	}

	public function testShouldReturnFalseWhenCannotLoadClass() {
		$this->application->withRootDir($this->files->get($this, ''));
		$this->application->withModule('test', $this->files->get($this, '/test'));

		self::assertFalse($this->application->loader->loadClass('ReturnFalse'));
		self::assertFalse($this->application->loader->loadClass('Test_Module\\ReturnFalse'));
	}

	public function testShouldReturnFalseWhenExceptionInLoadedFile() {
		$this->application->withRootDir($this->files->get($this, ''));

		self::assertFalse($this->application->loader->loadClass('ThrowsException'));
	}

	public function testLoadFileShouldReturnTrueWhenClassLoadedSuccessful() {
		self::assertTrue($this->application->loader->loadFileWithClass('FileWithSomeClass', __DIR__ . '/_files/FileWithClass.php'));
	}

	public function testLoadFileShouldReturnFalseWhenFileNotExists() {
		self::assertFalse($this->application->loader->loadFileWithClass('SomeClass', __FILE__ . '.test'));
	}

	public function testLoadFileShuldReturnFalseWhenCannotIncludeFile() {
		self::assertFalse($this->application->loader->loadFileWithClass('SomeClass', __DIR__ . '/_files/ReturnFalse.php'));
	}

	public function testLoadFileShouldReturnFalseWhenFileNotContainsRequiredClass() {
		self::assertFalse($this->application->loader->loadFileWithClass('SomeClass', __DIR__ . '/_files/NotSomeClass.php'));
	}

	/**
	 * @return void
	 * @param string $className
	 */
	protected function assertClassLoaded($className) {
		self::assertFalse(class_exists($className, false), 'Class should not be loaded before test');
		self::assertTrue($this->application->loader->loadClass($className), 'Loader should load given class by name');
		self::assertTrue(class_exists($className, false), 'Class should be loaded');
	}

	protected function tearDown() {
		set_include_path($this->includePath);
		unSet($this->includePath);

		parent::tearDown();
	}

}