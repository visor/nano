<?php

class NanoModulesTest extends TestUtils_TestCase {

	/**
	 * @var Nano_Modules
	 */
	private $modules;

	protected function setUp() {
		$this->modules = new Nano_Modules();
	}

	public function testPathes() {
		$this->modules
			->append('default')
			->append('other', '/tmp')
			->append('some', '/path2')
		;
		self::assertEquals('/tmp', $this->modules->getPath('other', null));
		self::assertEquals('/path2', $this->modules->getPath('some', null));
		self::assertEquals('/path2/views', $this->modules->getPath('some', 'views'));
		self::assertEquals(MODULES . DS  . 'default', $this->modules->getPath('default', null));
	}

	public function testActive() {
		self::assertFalse($this->modules->active('default'));
		self::assertFalse($this->modules->active('some'));
		self::assertFalse($this->modules->active('other'));

		$this->modules->append('default');
		self::assertTrue($this->modules->active('default'));
		self::assertFalse($this->modules->active('some'));
		self::assertFalse($this->modules->active('other'));

		$this->modules->append('some', '/path2');
		self::assertTrue($this->modules->active('default'));
		self::assertTrue($this->modules->active('some'));
		self::assertFalse($this->modules->active('other'));

		$this->modules->append('other', '/tmp');
		self::assertTrue($this->modules->active('default'));
		self::assertTrue($this->modules->active('some'));
		self::assertTrue($this->modules->active('other'));
	}

	protected function tearDown() {
		$this->modules = null;
	}

}