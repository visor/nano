<?php

/**
 * @group cache
 */
class CacheTest extends TestUtils_TestCase {

	public function testInstance() {
		self::assertSame(Cache::instance(), Cache::instance());
		self::assertSame(Cache::instance(), self::getObjectProperty('Cache', 'instance'));
	}

	public function testGetApi() {
		self::assertType('Cache_API_File', Cache::getApi('File'));
		self::assertType('Cache_API_MongoDb', Cache::getApi('MongoDb'));
		self::assertException(function() { Cache::getApi('SomeOtherApi'); }, 'Cache_Exception', '');
	}

	public function testGetValue() {
		self::markTestIncomplete();
	}

	public function testSetValue() {
		self::markTestIncomplete();
	}

	public function testClearValue() {
		self::markTestIncomplete();
	}

	public function testClearTags() {
		self::markTestIncomplete();
	}

}