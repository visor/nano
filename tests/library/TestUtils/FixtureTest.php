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
		$this->setObjectProperty(TestUtils_Fixture::instance()->forTest(), 'index', 0);
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

	public function testFixtureShouldRememberIndexes() {
		TestUtils_Fixture::instance()->forTest('default', 1);
		self::assertEquals(1, $this->getObjectProperty(TestUtils_Fixture::instance()->forTest(), 'index'));

		TestUtils_Fixture::instance()->forTest('default', 1);
		self::assertEquals(2, $this->getObjectProperty(TestUtils_Fixture::instance()->forTest(), 'index'));
	}

	public function testGetNewShouldReturnsLastRecord() {
		TestUtils_Fixture::instance()->forTest('default', 2);
		self::assertEquals(2, $this->getObjectProperty(TestUtils_Fixture::instance()->forTest(), 'index'));

		$record = TestUtils_Fixture::instance()->forTest()->getNew('default');
		self::assertType('ActiveRecordBasic', $record);
		self::assertEquals('example text for record 002', $record->text);
		self::assertSame($record, TestUtils_Fixture::instance()->forTest()->get('default', 2));

		self::assertEquals(3, $this->getObjectProperty(TestUtils_Fixture::instance()->forTest(), 'index'));
	}

	public function testGetCustomShouldCreateRecord() {
		$record = TestUtils_Fixture::instance()->forTest()->getCustom('default', array('text' => 'some custom text'));

		self::assertType('ActiveRecordBasic', $record);
		self::assertEquals('some custom text', $record->text);
		self::assertSame($record, TestUtils_Fixture::instance()->forTest()->get('default', 0));

		self::assertEquals(1, $this->getObjectProperty(TestUtils_Fixture::instance()->forTest(), 'index'));
	}

	protected function tearDown() {
		Nano::db()->rollBack();
		$this->setObjectProperty(TestUtils_Fixture::instance()->forTest(), 'index', 0);
	}

	public static function tearDownAfterClass() {
		ActiveRecordBasic::deleteTable();
	}

}