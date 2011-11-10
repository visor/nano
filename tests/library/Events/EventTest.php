<?php

/**
 * @group framework
 * @group events
 */
class Library_Events_EventTest extends TestUtils_TestCase {

	public function testPassingEventTypesIntoContructor() {
		$event = new Event('some-type');
		self::assertEquals('some-type', $event->getType());
	}

	public function testFactoryMethod() {
		self::assertInstanceOf('Event', Event::create('foo'));
		self::assertNotSame(Event::create('foo'), Event::create('foo'));
		self::assertEquals('bar', Event::create('bar')->getType());
	}

	public function testSetArgumentShouldReturnEventInstance() {
		self::assertInstanceOf('Event', Event::create('foo')->setArgument('bar', 'baz'));
	}

	public function testPassingArguments() {
		$event = Event::create('foo')->setArgument('bar', 'baz');
		self::assertEquals('baz', $event->getArgument('bar', 'foo'));
	}

	public function testGettingUndefinedArguments() {
		$event = Event::create('foo');
		self::assertNull($event->getArgument('foo'));
		self::assertEquals('bar', $event->getArgument('baz', 'bar'));
	}

}