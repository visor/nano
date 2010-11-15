<?php

/**
 * @group cache
 * @group framework
 */
class CacheApiMongoDbTest extends TestUtils_TestCase {

	const DATABASE_NAME   = 'nanoCacheTest';

	/**
	 * @var Cache_API_MongoDb
	 */
	protected $api;

	protected function setUp() {
		if (!class_exists('Mongo', false) || !extension_loaded('mongo')) {
			self::markTestSkipped('No mongo extension');
		}
		$this->connection->check('localhost', 27017, 'MongoDb not running on %s:%d.');

		$this->api = Cache::getApi('MongoDb');
		$this->api->configure((object)array(
			'server' => 'mongodb://localhost:27017/' . self::DATABASE_NAME
		));
		$this->api->collection()->remove(array());
	}

	public function testGetting() {
		self::assertEquals(0, $this->api->collection()->count());
		self::assertNull($this->api->get('someKey'));
		$object = (object)array('field1' => 'value1', 'field2' => 'value2');
		$this->api->collection()->insert(array(
			  'key'     => 'someKey'
			, 'value'   => new MongoBinData(serialize($object))
			, 'expires' => new MongoDate(time() + 1000)
			, 'tags'    => array()
		));
		$value = $this->api->get('someKey');
		self::assertNotNull($value);
		self::assertType('stdClass', $value);
		self::assertEquals($object, $value);
	}

	public function testGettingExpired() {
		self::assertEquals(0, $this->api->collection()->count());
		$this->api->collection()->insert(array(
			  'key'     => 'expiredCache'
			, 'value'   => 'someValue'
			, 'expires' => new MongoDate(time() - 1000)
			, 'tags'    => array()
		));
		self::assertEquals(1, $this->api->collection()->count());
		self::assertNull($this->api->get('expiredCache'));
		self::assertEquals(0, $this->api->collection()->count());
	}

	public function testSettingCache() {
		self::assertEquals(0, $this->api->collection()->count());
		$this->api->set('someKey', 'someValue', 1000, array('tag1', 'tag2'));
		self::assertEquals(1, $this->api->collection()->count());
		$record = $this->api->collection()->findOne(array('key' => 'someKey'));

		self::assertNotNull($record);
		self::assertEquals('someKey', $record['key']);
		self::assertEquals('someValue', $record['value']);
		self::assertEquals(array('tag1', 'tag2'), $record['tags']);
	}

	public function testClear() {
		self::assertEquals(0, $this->api->collection()->count());
		$this->api->collection()->insert(array(
			  'key'     => 'key1'
			, 'value'   => 'someValue'
			, 'expires' => new MongoDate(time() + 1000)
			, 'tags'    => array()
		));
		$this->api->collection()->insert(array(
			  'key'     => 'key2'
			, 'value'   => 'someValue'
			, 'expires' => new MongoDate(time() + 1000)
			, 'tags'    => array()
		));
		self::assertEquals(2, $this->api->collection()->count());
		$this->api->clear('key2');
		self::assertEquals(1, $this->api->collection()->count());
		self::assertNotNull($this->api->get('key1'));
		self::assertNull($this->api->get('key2'));
	}

	public function testClearTag() {
		self::assertEquals(0, $this->api->collection()->count());
		$collection  = $this->api->collection();
		$prepareData = function () use ($collection) {
			$collection->remove(array());
			$collection->insert(array(
				  'key'     => 'key1'
				, 'value'   => 'someValue'
				, 'expires' => new MongoDate(time() + 1000)
				, 'tags'    => array('tag1')
			));
			$collection->insert(array(
				  'key'     => 'key2'
				, 'value'   => 'someValue'
				, 'expires' => new MongoDate(time() + 1000)
				, 'tags'    => array('tag2')
			));
			$collection->insert(array(
				  'key'     => 'key3'
				, 'value'   => 'someValue'
				, 'expires' => new MongoDate(time() + 1000)
				, 'tags'    => array('tag1', 'tag2')
			));
			$collection->insert(array(
				  'key'     => 'key4'
				, 'value'   => 'someValue'
				, 'expires' => new MongoDate(time() + 1000)
				, 'tags'    => array('tag3')
			));
		};

		$prepareData();
		self::assertEquals(4, $this->api->collection()->count());
		$this->api->clearTag(array('tag1'));
		self::assertEquals(2, $this->api->collection()->count());
		self::assertNull($this->api->get('key1'));
		self::assertNotNull($this->api->get('key2'));
		self::assertNull($this->api->get('key3'));
		self::assertNotNull($this->api->get('key4'));

		$prepareData();
		self::assertEquals(4, $this->api->collection()->count());
		$this->api->clearTag(array('tag1', 'tag2'));
		self::assertEquals(1, $this->api->collection()->count());
		self::assertNull($this->api->get('key1'));
		self::assertNull($this->api->get('key2'));
		self::assertNull($this->api->get('key3'));
		self::assertNotNull($this->api->get('key4'));
	}

	public function testGarbage() {
		self::assertEquals(0, $this->api->collection()->count());
		$this->api->collection()->insert(array(
			  'key'     => 'expiredCache'
			, 'value'   => 'someValue'
			, 'expires' => new MongoDate(time() - 1000)
			, 'tags'    => array()
		));
		$this->api->collection()->insert(array(
			  'key'     => 'validCache'
			, 'value'   => 'someValue'
			, 'expires' => new MongoDate(time() + 1000)
			, 'tags'    => array()
		));
		self::assertEquals(2, $this->api->collection()->count());
		$this->api->garbage();
		self::assertEquals(1, $this->api->collection()->count());
	}

	protected function tearDown() {
		if ($this->api) {
			$this->api->collection()->remove(array());
			$this->api = null;
		}
	}

}