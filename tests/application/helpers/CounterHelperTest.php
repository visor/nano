<?php

/**
 * @group helpers
 */
class CounterHelperTest extends TestUtils_TestCase {

	/**
	 * @var CounterHelper
	 */
	private $helper = null;

	protected function setUp() {
		$this->resetCounter();
		$this->helper = new CounterHelper();
	}

	public function testDefaultCounter() {
		for ($i = 1; $i <= 10; ++$i) {
			self::assertEquals($i, $this->helper->increment());
			$data = self::getObjectProperty('CounterHelper', 'counters');
			self::assertArrayHasKey('', $data);
			self::assertEquals($i, $data['']);
		}
	}

	public function testNamedCounter() {
		self::assertEquals(1, $this->helper->increment('name1'));
		for ($i = 1; $i <= 10; ++$i) {
			self::assertEquals($i + 1, $this->helper->increment('name1'));
			self::assertEquals($i, $this->helper->increment('name2'));
			$data = self::getObjectProperty('CounterHelper', 'counters');
			self::assertArrayHasKey('name1', $data);
			self::assertArrayHasKey('name2', $data);
			self::assertEquals($i + 1, $data['name1']);
			self::assertEquals($i, $data['name2']);
		}
	}

	protected function tearDown() {
		$this->resetCounter();
	}

	private function resetCounter() {
		self::setObjectProperty('CounterHelper', 'counters', array());
	}

}