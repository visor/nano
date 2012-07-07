<?php

/**
 * @group library
 * @group events
 */
class Library_Events_ManagerTest extends \Nano\TestUtils\TestCase {

	/**
	 * @var \Nano\Event\Manager
	 */
	protected $manager;

	protected function setUp() {
		require_once $this->files->get($this, '/handlers.php');
		require_once $this->files->get($this, '/TestHandler.php');
		$this->manager = new \Nano\Event\Manager();
	}

	public function testAddingCallbackAsFunctionName() {
		$event = \Nano\Event::create('test-event');
		$this->manager->attach($event->getType(), 'library_events_handler_f1');
		$this->checkHandlerCalled($event);
	}

	public function testAddingCallbackAsClosure() {
		$event = \Nano\Event::create('test-event');
		$this->manager->attach($event->getType(), function (\Nano\Event $e) {
			$runs = $e->getArgument('runs', 0);
			++$runs;
			$e->setArgument('runs', $runs);
		});
		$this->checkHandlerCalled($event);
	}

	public function testAddingCallbackAsAnonynousFunction() {
		$event   = \Nano\Event::create('test-event');
		$handler = function (\Nano\Event $e) {
			$runs = $e->getArgument('runs', 0);
			++$runs;
			$e->setArgument('runs', $runs);
		};
		$this->manager->attach($event->getType(), $handler);
		$this->checkHandlerCalled($event);
	}

	public function testAddingCallbackAsInstanceMethod() {
		$event    = \Nano\Event::create('test-event');
		$instance = new Library_Events_Handler_C1();
		$this->manager->attach($event->getType(), array($instance, 'instanceHandler'));
		$this->checkHandlerCalled($event);
	}

	public function testAddingCallbackAsStaticMethod() {
		$event = \Nano\Event::create('test-event');
		$this->manager->attach($event->getType(), array('Library_Events_Handler_C1', 'staticHandler'));
		$this->checkHandlerCalled($event);
	}

	public function testIsCallbackExists() {
		self::assertFalse($this->manager->callbackExists('test-event'));
		$this->manager->attach('test-event', 'library_events_handler_f1');
		self::assertTrue($this->manager->callbackExists('test-event'));
	}

	public function testEventTriggering() {
		$event    = \Nano\Event::create('test-event');
		$instance = new Library_Events_Handler_C1();

		$this->manager
			->attach($event->getType(), 'library_events_handler_f1')
			->attach($event->getType(), array($instance, 'instanceHandler'))
			->attach($event->getType(), array('Library_Events_Handler_C1', 'staticHandler'))
		;

		self::assertSame($event, $this->manager->trigger($event));
		self::assertEquals(3, $event->getArgument('runs'));
	}

	public function testTriggeringEventWithoutHandlers() {
		self::assertFalse($this->manager->callbackExists('test-event'));
		$event = $this->manager->trigger('test-event');
		self::assertNull($event->getArgument('runs'));
	}

	public function testTriggeringEventFromItself() {
		$event = new \Nano\Event('test-event');
		$this->manager
			->attach($event->getType(), 'library_events_handler_f1')
		;
		$event->trigger($this->manager);
		self::assertEquals(1, $event->getArgument('runs'));
	}

	public function testPassingEventArguments() {
		$event = \Nano\Event::create('test-event');
		$this->manager->attach($event->getType(), 'library_events_handler_f1');
		$this->manager->trigger($event, array('text' => 'some-text'));
		self::assertEquals('[some-text]', $event->getArgument('text'));
	}

	public function testPassingWrongHandler() {
		$this->setExpectedException('\Nano\Event\Exception', 'Passed handler not callable');
		$this->manager->attach('test', 'library_events_handler_f2');
	}

	public function testHandlersOrder() {
		$event    = \Nano\Event::create('test-event');
		$instance = new Library_Events_Handler_C1();

		$this->manager
			->attach($event->getType(), 'library_events_handler_f1')
			->attach($event->getType(), array($instance, 'instanceHandler'))
			->attach($event->getType(), array('Library_Events_Handler_C1', 'staticHandler'))
		;

		self::assertSame($event, $this->manager->trigger($event));
		self::assertEquals(3, $event->getArgument('runs'));
		self::assertEquals('123', $event->getArgument('run-order'));
	}

	public function testHandlerInstanceShouldStoredOnce() {
		$handler = new Library_Events_TestHandler();
		$this->manager->attachHandler($handler);
		$this->manager->attachHandler($handler);

		$this->manager->trigger('some-event');
		self::assertEquals(1, $handler->someEventRised);
	}

	protected function tearDown() {
		unSet($this->manager);
	}

	protected function checkHandlerCalled(\Nano\Event $event) {
		self::assertEquals(0, $event->getArgument('runs', 0));
		$this->manager->trigger($event);
		self::assertEquals(1, $event->getArgument('runs', 0));
	}

}