<?php

/**
 * @group library
 * @group events
 */
class Library_Events_ManagerTest extends TestUtils_TestCase {

	/**
	 * @var Event_Manager
	 */
	protected $manager;

	protected function setUp() {
		require_once $this->files->get($this, '/handlers.php');
		$this->manager = new Event_Manager();
	}

	public function testAddingHandlerAsFunctionName() {
		$event = Event::create('test-event');
		$this->manager->attach($event->getType(), 'library_events_handler_f1');
		$this->checkHandlerCalled($event);
	}

	public function testAddingHandlerAsClosure() {
		$event = Event::create('test-event');
		$this->manager->attach($event->getType(), function (Event $e) {
			$runs = $e->getArgument('runs', 0);
			++$runs;
			$e->setArgument('runs', $runs);
		});
		$this->checkHandlerCalled($event);
	}

	public function testAddingHandlerAsAnonynousFunction() {
		$event   = Event::create('test-event');
		$handler = function (Event $e) {
			$runs = $e->getArgument('runs', 0);
			++$runs;
			$e->setArgument('runs', $runs);
		};
		$this->manager->attach($event->getType(), $handler);
		$this->checkHandlerCalled($event);
	}

	public function testAddingHandlerAsInstanceMethod() {
		$event    = Event::create('test-event');
		$instance = new Library_Events_Handler_C1();
		$this->manager->attach($event->getType(), array($instance, 'instanceHandler'));
		$this->checkHandlerCalled($event);
	}

	public function testAddingHandlerAsStaticMethod() {
		$event = Event::create('test-event');
		$this->manager->attach($event->getType(), array('Library_Events_Handler_C1', 'staticHandler'));
		$this->checkHandlerCalled($event);
	}

	public function testIsHandlerExists() {
		self::assertFalse($this->manager->handlerExists('test-event'));
		$this->manager->attach('test-event', 'library_events_handler_f1');
		self::assertTrue($this->manager->handlerExists('test-event'));
	}

	public function testEventTriggering() {
		$event    = Event::create('test-event');
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
		self::assertFalse($this->manager->handlerExists('test-event'));
		$event = $this->manager->trigger('test-event');
		self::assertNull($event->getArgument('runs'));
	}

	public function testTriggeringEventFromItself() {
		$event = new Event('test-event');
		$this->manager
			->attach($event->getType(), 'library_events_handler_f1')
		;
		$event->trigger($this->manager);
		self::assertEquals(1, $event->getArgument('runs'));
	}

	public function testPassingEventArguments() {
		$event = Event::create('test-event');
		$this->manager->attach($event->getType(), 'library_events_handler_f1');
		$this->manager->trigger($event, array('text' => 'some-text'));
		self::assertEquals('[some-text]', $event->getArgument('text'));
	}

	public function testPassingWrongHandler() {
		$this->setExpectedException('Event_Exception', 'Passed handler not callable');
		$this->manager->attach('test', 'library_events_handler_f2');
	}

	public function testHandlersOrder() {
		$event    = Event::create('test-event');
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

	protected function tearDown() {
		unSet($this->manager);
	}

	protected function checkHandlerCalled(Event $event) {
		self::assertEquals(0, $event->getArgument('runs', 0));
		$this->manager->trigger($event);
		self::assertEquals(1, $event->getArgument('runs', 0));
	}

}