<?php

/**
 * @group cache
 * @group framework
 */
class CacheTicketTest extends TestUtils_TestCase {

	/**
	 * @var Cache_API_MongoDb
	 */
	protected $cache = null;

	public static function setUpBeforeClass() {
		require_once __DIR__ . '/_files/ExampleCacheTicket.php';
	}

	protected function setUp() {
		if (!class_exists('Mongo', false) || !extension_loaded('mongo')) {
			self::markTestSkipped('No mongo extension');
		}
		$this->connection->check('localhost', 27017, 'MongoDb not running on %s:%d.');

		$this->cache = Cache::getApi('MongoDb');
		$this->cache->configure((object)array('server'  => 'mongodb://localhost:27017/' . CacheApiMongoDbTest::DATABASE_NAME));
		$this->cache->collection()->remove(array());
	}

	public function testChildClassStatics() {
		self::assertType('ExampleCacheTicket', ExampleCacheTicket::create(array('id' => 1, 'key' => 2)));
		self::assertType('ExampleCacheTicket', ExampleCacheTicket::load(array('id' => 1, 'key' => 2)));
	}

	public function testLoadingFromCache() {
		ExampleCacheTicket::setCache($this->cache);
		self::assertFalse(ExampleCacheTicket::load(array('id' => 1, 'key' => 2))->valid());
		$this->cache->set('1-2', 'some stored value');
		self::assertTrue(ExampleCacheTicket::load(array('id' => 1, 'key' => 2))->valid());
		self::assertNotNull($this->cache->get('1-2'));
		self::assertEquals('some stored value', ExampleCacheTicket::load(array('id' => 1, 'key' => 2))->getData());
	}

	public function testSavingToCache() {
		ExampleCacheTicket::setCache($this->cache);
		$ticket = ExampleCacheTicket::create(array('id' => 1, 'key' => 2));
		self::assertFalse($ticket->valid());
		$ticket->setData('some cached value')->save();
		self::assertTrue($ticket->valid());
		self::assertNotNull($this->cache->get('1-2'));
		self::assertEquals('some cached value', $this->cache->get('1-2'));
		self::assertEquals('some cached value', $ticket->getData());

		$ticket->setData('some other cached value')->save();
		self::assertTrue($ticket->valid());
		self::assertNotNull($this->cache->get('1-2'));
		self::assertEquals('some other cached value', $this->cache->get('1-2'));
		self::assertEquals('some other cached value', $ticket->getData());
	}

	protected function tearDown() {
		if ($this->cache) {
			$this->cache->collection()->remove(array());
			$this->cache = null;
		}
	}

}