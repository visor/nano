<?php

/**
 * @group core
 * @group framework
 */
class Core_MessageTest extends TestUtils_TestCase {

	protected function setUp() {
		Nano_Message::instance(true)->lang('');
	}

	public function testLoadFileNotFound() {
		$this->setExpectedException('Exception', 'File "' . Application::current()->getRootDir() . DS . 'messages' . DS . 'not-exists.php" not found');
		Nano_Message::instance()->load('not-exists');
	}

	public function testLoadFileNoMessages() {
		$this->setExpectedException('Exception', 'No strings loaded from file "empty"');
		Nano_Message::instance()->load('empty');
	}

	public function testLoadMessageFile() {
		Nano_Message::instance()->load('default');
	}

	public function testGetMessages() {
		self::assertNull(Nano_Message::instance()->m('m1'));
		Nano_Message::instance()->load('test');
		self::assertEquals('message 1', Nano_Message::instance()->m('m1'));
	}

	public function testFormatPlural() {
		self::assertNull(Nano_Message::instance()->p(1, 'p1'));
		Nano_Message::instance()->load('test');
		self::assertNull(Nano_Message::instance()->p(1, 'p1'));
		Nano_Message::instance()->lang('ru');
		self::assertEquals('one', Nano_Message::instance()->p(1, 'p1'));
		self::assertEquals('two', Nano_Message::instance()->p(2, 'p1'));
		self::assertEquals('many', Nano_Message::instance()->p(5, 'p1'));
	}

	public function testFormatMessages() {
		self::assertNull(Nano_Message::instance()->f('f1', 1, 's'));
		Nano_Message::instance()->load('test');
		self::assertEquals('format 01 s', Nano_Message::instance()->f('f1', 1, 's'));
		self::assertEquals('format 01 s', Nano_Message::instance()->fa('f1', array(1, 's')));
	}

	protected function tearDown() {
		Nano_Message::instance(true);
	}

}