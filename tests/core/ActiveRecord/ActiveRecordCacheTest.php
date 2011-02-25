<?php

/**
 * @group active-record
 * @group framework
 */
class ActiveRecordCacheTest extends TestUtils_TestCase {

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

	protected function tearDown() {
		ActiveRecordBasic::deleteTable();
		Cache::clear();
		Nano::config()->set('cache', $this->defaultConfig);
		Cache::invalidateInstance();
	}


}