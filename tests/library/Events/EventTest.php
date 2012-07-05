<?php

/**
 * @group library
 * @group events
 */
class Library_Events_EventTest extends TestUtils_TestCase {

	public function testPassingEventTypesIntoContructor() {
		$event = new \Nano\Event('some-type');
		self::assertEquals('some-type', $event->getType());
	}

	public function testFactoryMethod() {
		self::assertInstanceOf('\Nano\Event', \Nano\Event::create('foo'));
		self::assertNotSame(\Nano\Event::create('foo'), \Nano\Event::create('foo'));
		self::assertEquals('bar', \Nano\Event::create('bar')->getType());
	}

	public function testSetArgumentShouldReturnEventInstance() {
		self::assertInstanceOf('\Nano\Event', \Nano\Event::create('foo')->setArgument('bar', 'baz'));
	}

	public function testPassingArguments() {
		$event = \Nano\Event::create('foo')->setArgument('bar', 'baz');
		self::assertEquals('baz', $event->getArgument('bar', 'foo'));
	}

	public function testGettingUndefinedArguments() {
		$event = \Nano\Event::create('foo');
		self::assertNull($event->getArgument('foo'));
		self::assertEquals('bar', $event->getArgument('baz', 'bar'));
	}

}