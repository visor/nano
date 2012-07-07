<?php

require_once __DIR__ . '/Abstract.php';

/**
 * @group core
 */
class Core_Application_LoaderTest extends Core_Application_Abstract {

	protected $includePath = '';

	protected function setUp() {
		parent::setUp();

		\Nano::setApplication($this->application);
		$this->includePath = get_include_path();
	}

	public function testLoaderShouldInitializedWhenApplicationCreated() {
		self::assertInstanceOf('\Nano\\Loader', $this->application->loader);
	}

	public function testNanoClassShouldBeLoaded() {
		self::assertTrue(class_exists('Nano', false));
	}

	public function testLoadingApplicationLibraryClass() {
		$this->application->withRootDir($this->files->get($this, ''));

		$this->assertClassLoaded('App\TestApplicationLibraryClass');
	}

	public function testLoadingModuleClasses() {
		$this->application->withModule('test', $this->files->get($this, '/test'));

		$this->assertClassLoaded('Module\Test\Controller\Class2');
		$this->assertClassLoaded('Module\Test\Library2Class');
		$this->assertClassLoaded('Module\Test\Model\Class2');
		$this->assertClassLoaded('Module\Test\Plugin\Class2');
	}

	public function testShouldReturnTrueWhenClassAlreadyLoaded() {
		$this->application->withRootDir($this->files->get($this, ''));
		self::assertTrue($this->application->loader->loadClass('App\DoubleLoaded'));
		self::assertTrue($this->application->loader->loadClass('App\DoubleLoaded'));
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