<?php

/**
 * @group active-record
 */
class ActiveRecordSearchTest extends TestUtils_TestCase {

	public static function setUpBeforeClass() {
		require_once __DIR__ . '/_files/ActiveRecordBasic.php';
		require_once __DIR__ . '/_files/ActiveRecordCustomPk.php';
	}

	protected function setUp() {
		ActiveRecordCustomPk::deleteTable();
		ActiveRecordCustomPk::createTable();
		ActiveRecordBasic::deleteTable();
		ActiveRecordBasic::createTable();
		Nano::db()->log()->clean();
	}

	public function testActiveRecordFactory() {
		self::assertType('ActiveRecordBasic', ActiveRecordBasic::create());
	}

	public function testPrimaryKeyValue() {
		$record = new ActiveRecordBasic();
		self::assertEquals(array(),                 $record->getWhereFields(null));
		$record->id = 2;
		self::assertEquals(array('id' => 1),        $record->getWhereFields(array('id' => 1)));
		self::assertEquals(array('id' => 1),        $record->getWhereFields(1));
		self::assertEquals(array('text' => 'text'), $record->getWhereFields(array('text' => 'text')));
		self::assertEquals(array('id' => 2),        $record->getWhereFields(null));

		$record = new ActiveRecordCustomPk();
		$record->id1 = 10;
		$record->id2 = 20;
		self::assertEquals(array('id1' => 1),               $record->getWhereFields(array('id1' => 1)));
		self::assertEquals(array('id1' => 1,  'id2' => 2),  $record->getWhereFields(array('id1' => 1,  'id2' => 2)));
		self::assertEquals(array('id1' => 10, 'id2' => 20), $record->getWhereFields(1));
		self::assertEquals(array('id1' => 10, 'id2' => 20), $record->getWhereFields(null));
		self::assertEquals(array('text' => 'text'),         $record->getWhereFields(array('text' => 'text')));
	}

	public function testSelectCriteria() {
		$record = new ActiveRecordBasic();
		self::assertEquals("(id = '1')", $record->getSelectCriteria(array('id' => 1))->toString(Nano::db()));
		self::assertEquals("(id = '2' and text = 'some')", $record->getSelectCriteria(array('id' => 2, 'text' => 'some'))->toString(Nano::db()));
		self::assertEquals('', $record->getSelectCriteria(null)->toString(Nano::db()));
	}

	public function testSelectingOne() {
		for ($i = 0; $i < 10; $i++) {
			Nano::db()->insert(ActiveRecordBasic::TABLE_NAME, array('text' => 'record #' . sprintf('%03d', $i)));
		}
		$record = ActiveRecordBasic::create()->findOne(array('id' => 1));
		/** @var $record ActiveRecordBasic */
		self::assertType('ActiveRecordBasic', $record);
		self::assertEquals(1, $record->id);
		self::assertEquals('record #000', $record->text);

		$record = ActiveRecordBasic::create()->findOne(1);
		/** @var $record ActiveRecordBasic */
		self::assertType('ActiveRecordBasic', $record);
		self::assertEquals(1, $record->id);
		self::assertEquals('record #000', $record->text);
	}

	public function testSelectingSeveralEntries() {
		for ($i = 1; $i <= 12; $i++) {
			$text = 'record #' . sprintf('%03d', $i % 3);
			Nano::db()->insert(ActiveRecordBasic::TABLE_NAME, array('text' => $text));
		}

		$records = ActiveRecordBasic::create()->find(array('text' => 'record #002'));
		self::assertType('Nano_Db_Statement', $records);
		self::assertEquals(4, $records->rowCount());

		$record = $records->fetch();
		/** @var $record ActiveRecordBasic */
		self::assertEquals('record #002', $record->text);

		$record = ActiveRecordBasic::create();
		/** @var $record ActiveRecordBasic */
		$record->text = 'record #001';
		$records = $record->find();
		self::assertType('Nano_Db_Statement', $records);
		self::assertEquals(4, $records->rowCount());

		$record = $records->fetch();
		/** @var $record ActiveRecordBasic */
		self::assertEquals('record #001', $record->text);

		$records = $record->find();
		self::assertType('Nano_Db_Statement', $records);
		self::assertEquals(1, $records->rowCount());
		$found = $records->fetch();
		/** @var $found ActiveRecordBasic */
		self::assertEquals($record->id, $found->id);
		self::assertEquals($record->text, $found->text);

		$record = ActiveRecordBasic::create();
		/** @var $record ActiveRecordBasic */
		$record->setLimit(2, 0);
		$record->text = 'record #000';
		$records = $record->find();
		self::assertType('Nano_Db_Statement', $records);
		self::assertEquals(2, $records->rowCount());
	}

	protected function tearDown() {
		ActiveRecordCustomPk::deleteTable();
		ActiveRecordBasic::deleteTable();
		Nano::db()->log()->clean();
	}

}