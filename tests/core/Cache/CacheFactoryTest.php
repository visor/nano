<?php

/**
 * @group cache
 * @group framework
 */
class CacheTest extends TestUtils_TestCase {

	private static $cacheConfig;

	public static function setUpBeforeClass() {
		require_once __DIR__ . '/_files/Cache_API_Fake.php';
		require_once __DIR__ . '/_files/Cache_API_NotCache.php';
	}

	public function testInstance() {
		self::assertSame(Cache::instance(), Cache::instance());
		self::assertSame(Cache::instance(), self::getObjectProperty('Cache', 'instance'));
	}

	public function testInvalidInstance() {
		self::assertException(function() { Cache::getApi('NotFound'); }, 'Cache_Exception', 'Cache implementation Cache_API_NotFound not found');
		self::assertException(function() { Cache::getApi('NotCache'); }, 'Cache_Exception', 'Invalid cache implementation specified: Cache_API_NotCache');
	}

	public function testGetApi() {
		self::assertInstanceOf('Cache_API_File', Cache::getApi('File'));
		self::assertInstanceOf('Cache_API_MongoDb', Cache::getApi('MongoDb'));
		self::assertException(function() { Cache::getApi('SomeOtherApi'); }, 'Cache_Exception', '');
	}

	public function testConfigure() {
		$this->setupFakeCache();
		self::assertInstanceOf('Cache_API_Fake', Cache::instance());
		self::assertEquals('value', Cache::instance()->config->property);
	}

	public function testGetValue() {
		$this->setupFakeCache();
		self::assertEquals(null, Cache::get('some-invalid-key'));
		self::assertEquals('some-valid-key', Cache::get('some-valid-key'));
	}

	public function testSetValue() {
		$this->setupFakeCache();
		Cache::set('key', 'value', 1000, array('tag1', 'tag2'));
		self::assertEquals('key', Cache::instance()->lastSet[0]);
		self::assertEquals('value', Cache::instance()->lastSet[1]);
		self::assertEquals(1000, Cache::instance()->lastSet[2]);
		self::assertEquals(array('tag1', 'tag2'), Cache::instance()->lastSet[3]);
	}

	public function testClearValue() {
		$this->setupFakeCache();
		$key = array('some-tags-to-delete');
		Cache::clear($key);
		self::assertEquals($key, Cache::instance()->lastClear);
	}

	public function testClearTags() {
		$this->setupFakeCache();
		$tags = array('some', 'tags', 'to', 'delete');
		Cache::clearTag($tags);
		self::assertEquals($tags, Cache::instance()->lastClearTag);
	}

	protected function setupFakeCache() {
		Nano::config()->set('cache', (object)array(
			  'api' => 'Fake'
			, 'fake' => (object)array(
				'property' => 'value'
			)
		));
		self::setObjectProperty('Cache', 'instance', null);
	}

}