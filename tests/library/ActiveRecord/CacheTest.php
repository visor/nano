<?php

/**
 * @group active-record
 * @group library
 */
class Library_ActiveRecord_CacheTest extends TestUtils_TestCase {

	protected $cacheConfig;

	protected $defaultConfig;

	public static function setUpBeforeClass() {
		require_once __DIR__ . '/_files/ActiveRecordBasic.php';
	}

	protected function setUp() {
		$this->defaultConfig = Nano::config('cache');
		Nano::config()->set('cache', (object)array(
			  'api'      => 'MongoDb'
			, 'mongodb'  => (object)array(
				'server' => 'localhost'
			)
			, 'database' => true
		));
		Cache::invalidateInstance();
		ActiveRecordBasic::deleteTable();
		ActiveRecordBasic::createTable();
		Cache::clear();

		for ($i = 1; $i <= 10; $i++) {
			$text = 'record #' . sprintf('%02d', $i % 5);
			Nano::db()->insert(ActiveRecordBasic::TABLE_NAME, array('text' => $text));
		}
		Nano::db()->log()->clean();
	}

	public function testLoadingSingleRecordFromCache() {
		$firstRecord = ActiveRecordBasic::prototype()->findOne(array('text' => 'record #01'));
		self::assertInstanceOf('ActiveRecordBasic', $firstRecord);
		self::assertEquals(1, Nano::db()->log()->count());

		$cachedRecord = ActiveRecordBasic::prototype()->findOne(array('text' => 'record #01'));
		self::assertInstanceOf('ActiveRecordBasic', $cachedRecord);
		self::assertEquals($firstRecord, $cachedRecord);
		self::assertEquals(1, Nano::db()->log()->count());
	}

	public function testInvalidatingCacheAfterUpdatingTable() {
		$record = ActiveRecordBasic::prototype()->findOne(array('text' => 'record #01'));
		self::assertEquals(1, Nano::db()->log()->count());
		ActiveRecordBasic::prototype()->findOne(array('text' => 'record #01'));
		self::assertEquals(1, Nano::db()->log()->count());

		$record->text = 'changed';
		$record->save();

		ActiveRecordBasic::prototype()->findOne(array('text' => 'changed'));
		ActiveRecordBasic::prototype()->findOne(array('text' => 'changed'));
		self::assertEquals(3, Nano::db()->log()->count());
	}

	public function testInvalidatingCacheAfterNewRecord() {
		$first  = ActiveRecordBasic::prototype()->findOne(array('text' => 'record #01'));
		ActiveRecordBasic::instance()->populate(array('text' => 'new'))->save();
		$second = ActiveRecordBasic::prototype()->findOne(array('text' => 'record #01'));

		self::assertEquals(3, Nano::db()->log()->count());
		self::assertEquals($first, $second);
	}

	public function testInvalidatingCacheAfterRemoveRecord() {
		$first  = ActiveRecordBasic::prototype()->findOne(array('text' => 'record #01'));
		ActiveRecordBasic::instance()->populate(array('text' => 'record #02'))->delete();
		$second = ActiveRecordBasic::prototype()->findOne(array('text' => 'record #01'));

		self::assertEquals(3, Nano::db()->log()->count());
		self::assertEquals($first, $second);
	}

	protected function tearDown() {
		ActiveRecordBasic::deleteTable();
		Cache::clear();
		Nano::config()->set('cache', $this->defaultConfig);
		Cache::invalidateInstance();
	}

}