<?php

/**
 * @group test-utils
 */
class TestUtils_FixtureTest extends TestUtils_TestCase {

	public static function setUpBeforeClass() {
		require_once TESTS . DS . 'core' . DS . 'ActiveRecord' . DS . '_files' . DS . 'ActiveRecordBasic.php';
		require_once __DIR__ . DS . '_files' . DS . 'TestFixtureForTest.php';
		ActiveRecordBasic::deleteTable();
		ActiveRecordBasic::createTable();
	}

	protected function setUp() {
		Nano::db()->beginTransaction();
	}

	public function testFixtureShouldLoad() {
		self::assertType('TestFixtureForTest', TestUtils_Fixture::instance()->forTest());
	}

	public function testFixtureShouldCreateOneRecord() {
		TestUtils_Fixture::instance()->forTest('default');
		self::assertEquals(1, ActiveRecordBasic::prototype()->count());

		$record = ActiveRecordBasic::prototype()->findOne();
		self::assertType('ActiveRecordBasic', $record);
		self::assertEquals('example text for record 000', $record->text);
	}

	public function testFixtureShouldCreateTenRecords() {
		TestUtils_Fixture::instance()->forTest('default', 10);
		self::assertEquals(10, ActiveRecordBasic::prototype()->count());
	}

	public function testFixtureShouldReturnRecord() {
		TestUtils_Fixture::instance()->forTest('default', 2);
		self::assertEquals(2, ActiveRecordBasic::prototype()->count());

		self::assertType('ActiveRecordBasic', TestUtils_Fixture::instance()->forTest()->get('default', 0));
		self::assertEquals('example text for record 000', TestUtils_Fixture::instance()->forTest()->get('default', 0)->text);

		self::assertType('ActiveRecordBasic', TestUtils_Fixture::instance()->forTest()->get('default', 1));
		self::assertEquals('example text for record 001', TestUtils_Fixture::instance()->forTest()->get('default', 1)->text);
	}

	protected function tearDown() {
		Nano::db()->rollBack();
	}

	public static function tearDownAfterClass() {
		ActiveRecordBasic::deleteTable();
	}

}