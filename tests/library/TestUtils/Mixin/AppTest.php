<?php

class TestUtils_Mixin_AppTest extends TestUtils_TestCase {

	/**
	 * @var \Nano\Application
	 */
	protected $app;

	/**
	 * @var TestUtils_Mixin_App
	 */
	protected $mixin;

	protected function setUp() {
		$this->app   = \Nano::app();
		$this->mixin = new TestUtils_Mixin_App;
	}

	public function testShouldStoreApplicationIntoInternalPropertyWhenBackup() {
		$app = Nano::app();
		$this->mixin->backup();
		self::assertInstanceOf('\Nano\Application', self::getObjectProperty($this->mixin, 'backup'));
		self::assertSame($app, self::getObjectProperty($this->mixin, 'backup'));
		self::assertNull(Nano::app());
	}

	public function testShouldStoreApplicationOnlyOnce() {
		$original = Nano::app();
		$this->mixin->backup();
		$new = \Nano\Application::create()->withConfigurationFormat('php')->withRootDir(__DIR__)->configure();
		$this->mixin->backup();

		self::assertInstanceOf('\Nano\Application', self::getObjectProperty($this->mixin, 'backup'));
		self::assertSame($original, self::getObjectProperty($this->mixin, 'backup'));
		self::assertNull(Nano::app());
	}

	public function testShouldRestoreApplication() {
		$original = Nano::app();
		$this->mixin->backup();
		self::assertNull(Nano::app());
		$this->mixin->restore();
		self::assertSame($original, Nano::app());
	}

	public function testShouldRestoreOnlyWhenBackupExists() {
		$original = Nano::app();
		$this->mixin->backup();
		self::assertNull(Nano::app());
		$this->mixin->restore();
		$this->mixin->restore();
		self::assertSame($original, Nano::app());
	}

	public function testRestoreShouldClearInternalProperty() {
		$this->mixin->backup();
		$this->mixin->restore();
		self::assertNull(self::getObjectProperty($this->mixin, 'backup'));
	}

	protected function tearDown() {
		\Nano::setApplication(null);
		\Nano::setApplication($this->app);
	}


}