<?php

/**
 * @group library
 * @group events
 */
class Library_Events_LoaderTest extends \Nano\TestUtils\TestCase {

	/**
	 * @var \Nano\Event\Manager
	 */
	protected $manager;

	protected function setUp() {
		$this->manager = new \Nano\Event\Manager();
		include_once __DIR__ . '/_files/handlers.php';
	}

	public function testLoadingHandlersFromSingleFile() {
		$this->manager->loader()->useFile(__DIR__ . '/_files/single-file.php');
		$this->checkHandlersLoaded('test-event', 1);
		$this->checkHandlersLoaded('another-test-event', 1);
	}

	public function testLoadFromFileShouldIgnoreNotExistedFile() {
		$this->manager->loader()->useFile(__DIR__ . '/_files/not-exists.php');
		$this->manager->loader()->load($this->manager);
	}

	public function testLoadingHandlersFromDirectory() {
		$this->manager->loader()->useDirectory(__DIR__ . '/_files/dir');
		$this->checkHandlersLoaded('test-event', 3);
	}

	public function testLoadingHandlersFromDirectoryWithFileMask() {
		$this->manager->loader()->useDirectory(__DIR__ . '/_files/dir', '*-loaded.php');
		$this->checkHandlersLoaded('test-event', 2);
	}

	public function testNoEventShouldLoadedUntilFirstEventTriggered() {
		$this->manager->loader()->useFile(__DIR__ . '/_files/single-file.php');

		$handlers = self::getObjectProperty($this->manager, 'handlers');
		self::assertCount(0, $handlers);
	}

	public function testPassingOneFileOrDirectoryTwice() {
		$this->manager->loader()->useFile(__DIR__ . '/_files/single-file.php');
		$this->manager->loader()->useDirectory(__DIR__ . '/_files/dir', '*-loaded.php');

		$this->manager->loader()->useFile(__DIR__ . '/_files/single-file.php');
		$this->manager->loader()->useDirectory(__DIR__ . '/_files/dir', '*.ignored');

		$this->checkHandlersLoaded('test-event', 2);
	}

	public function testLoadShouldRunsOnce() {
		$this->testLoadingHandlersFromSingleFile();
		$this->manager->loader()->load($this->manager);
		$this->testLoadingHandlersFromSingleFile();
	}

	protected function checkHandlersLoaded($eventName, $handlersCount) {
		$this->manager->trigger('nop'); //trigger fake event to load event using loader

		self::assertTrue($this->manager->callbackExists($eventName));
		$callbacks = self::getObjectProperty($this->manager, 'callbacks');
		self::assertInstanceOf('\Nano\Event\Queue', $callbacks->offsetGet($eventName));
		self::assertEquals($handlersCount, $callbacks->offsetGet($eventName)->count());
	}

	protected function tearDown() {
		unSet($this->manager);
	}

}