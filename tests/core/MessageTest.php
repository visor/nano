<?php

/**
 * @group core
 */
class Core_MessageTest extends TestUtils_TestCase {

	/**
	 * @var Application
	 */
	protected $application;

	/**
	 * @var Nano_Message
	 */
	protected $message;

	protected function setUp() {
		$this->application = new Application();
		$this->application
			->withConfigurationFormat('php')
			->withRootDir(dirName(dirName(__DIR__)) . DS . 'application-example')
			->configure()
		;
		$this->message = $this->application->message;
	}

	public function testLoadFileNotFound() {
		$this->setExpectedException('Nano_Exception', 'File "' . $this->application->rootDir . DS . 'messages' . DS . 'not-exists.php" not found');
		$this->message->load('not-exists');
	}

	public function testLoadFileNoMessages() {
		$this->setExpectedException('Nano_Exception', 'No strings loaded from file "empty"');
		$this->message->load('empty');
	}

	public function testLoadMessageFile() {
		$this->message->load('default');
	}

	public function testGetMessages() {
		self::assertNull($this->message->m('m1'));
		$this->message->load('test');
		self::assertEquals('message 1', $this->message->m('m1'));
	}

	public function testFormatPlural() {
		self::assertNull($this->message->p(1, 'p1'));
		$this->message->load('test');
		self::assertNull($this->message->p(1, 'p1'));
		$this->message->lang('ru');
		self::assertEquals('one', $this->message->p(1, 'p1'));
		self::assertEquals('two', $this->message->p(2, 'p1'));
		self::assertEquals('many', $this->message->p(5, 'p1'));
	}

	public function testFormatMessages() {
		self::assertNull($this->message->f('f1', 1, 's'));
		$this->message->load('test');
		self::assertEquals('format 01 s', $this->message->f('f1', 1, 's'));
		self::assertEquals('format 01 s', $this->message->fa('f1', array(1, 's')));
	}

	protected function tearDown() {
		unSet($this->message, $this->application);
	}

}