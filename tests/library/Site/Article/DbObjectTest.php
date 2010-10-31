<?php

/**
 * @group articles
 */
class Article_DbObjectTest extends TestUtils_TestCase {

	public static function setUpBeforeClass() {
		require_once __DIR__ . DS . '_files' . DS . 'TestArticle.php';
		TestArticle::dropTable();
		TestArticle::createTable();
	}

	public static function tearDownAfterClass() {
		TestArticle::dropTable();
	}

	public function testCreatedElementClass() {
		$item = TestArticle::createNew('title', 'body');
		self::assertType('TestArticle', $item);
	}

	public function testDefaultValuesAfterCreate() {
		$item = TestArticle::createNew('title', 'body');
		$item->save();

		self::assertFalse($item->isNew());
		self::assertNotNull($item->article_id);
		self::assertNotNull($item->created);
		self::assertNotNull($item->modified);
		self::assertNull($item->published);
		self::assertEquals($item->created, $item->modified);
		self::assertEquals(0, $item->active);

		return $item;
	}

	public function testDefaultValuesSetted() {
		$item = TestArticle::createNew('title', 'body');
		$item->active = 1;
		$item->save();

		self::assertEquals(1, $item->active);
	}

	public function testDatesAfterModify() {
		$item = TestArticle::createNew('title', 'body');
		$item->save();

		$created  = $item->created;
		$modified = $item->modified;

		Date::invalidateNow();
		sleep(2);
		$item->save();

		self::assertEquals($created, $item->created);
		self::assertNotEquals($created, $item->modified);
		self::assertNotEquals($modified, $item->modified);
	}

	/**
	 * @depends testDefaultValuesAfterCreate
	 */
	public function testGetOneItem() {
		$item   = $this->testDefaultValuesAfterCreate();
		$loaded = TestArticle::get($item->id());
		self::assertType('TestArticle', $loaded);
		self::assertEquals($item, $loaded);
	}

	public function testGetPublishedItems() {
		$this->createItems(100);
		self::assertEquals(0,  TestArticle::getPublished(null,                      null, null)->rowCount());
		self::assertEquals(1,  TestArticle::getPublished(Date::create('+3 days'),   null, null)->rowCount());
		self::assertEquals(2,  TestArticle::getPublished(Date::create('+5 days'),   null, null)->rowCount());
		self::assertEquals(50, TestArticle::getPublished(Date::create('+150 days'), null, null)->rowCount());
		self::assertEquals(10, TestArticle::getPublished(Date::create('+150 days'),    1,   10)->rowCount());
		self::assertEquals(10, TestArticle::getPublished(Date::create('+150 days'),    5,   10)->rowCount());
	}

	public function testGetAllItems() {
		$this->createItems(100);
		self::assertEquals(100, TestArticle::getAll(null, null)->rowCount());
		self::assertEquals(10,  TestArticle::getAll(1, 10)->rowCount());
	}

	protected function setUp() {
		self::markTestSkipped('need refactoring');
		Nano::db()->delete(TestArticle::NAME);
	}

	protected function tearDown() {
		Nano::db()->delete(TestArticle::NAME);
	}

	private function createItems($n) {
		for ($i = 1; $i <= $n; ++$i) {
			$text = sprintf('%05d', $i);
			$item = TestArticle::createNew('title ' . $text, 'body ' . $text);
			if (0 == ($i % 2)) {
				$date = Date::create('+' . $i . 'days');
				$item->publish($date);
			} else {
				$item->unpublish();
			}
			$item->save();
		}
	}

}