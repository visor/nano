<?php

/**
 * @group active-record
 */
class ActiveRecordCRUDTest extends TestUtils_TestCase {

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

	public function testCreate() {
		$record = new ActiveRecordBasic();
		$record->text = 'some text';
		$record->save();

		self::assertEquals(1, Nano::db()->log()->count());
		self::assertEquals("insert into `" . ActiveRecordBasic::TABLE_NAME . "`(`text`) values ('some text')", Nano::db()->log()->getLastQuery());
		self::assertNotNull($record->id);
		self::assertEquals(1, $record->id);
		self::assertFalse($record->isNew());

		$record = new ActiveRecordCustomPk();
		$record->text = 'some text';
		self::assertException(function () use ($record) { $record->save(); }, 'PDOException', 'Integrity constraint violation');

		$record->id1 = 10;
		self::assertException(function () use ($record) { $record->save(); }, 'PDOException', 'Integrity constraint violation');

		$record->id2 = 20;
		$record->save();

		self::assertEquals(2, Nano::db()->log()->count());
		self::assertEquals("insert into `" . ActiveRecordCustomPk::TABLE_NAME . "`(`id1`, `id2`, `text`) values ('10', '20', 'some text')", Nano::db()->log()->getLastQuery());
		self::assertFalse($record->isNew());
	}

	public function testUpdate() {
		$record = new ActiveRecordBasic();
		$record->text = 'some text';
		$record->save();
		self::assertNotNull($record->id);
		Nano::db()->log()->clean();

		$record->text = 'some other text';
		$record->save();
		self::assertEquals("update `" . ActiveRecordBasic::TABLE_NAME . "` set `text` = 'some other text' where (id = '1')", Nano::db()->log()->getLastQuery());
		Nano::db()->log()->clean();

		$record = new ActiveRecordCustomPk(array(
			  'id1'  => 1
			, 'id2'  => 2
			, 'text' => 'some text'
		));
		self::assertFalse($record->isNew());
		$record->save();
		self::assertEquals(0, Nano::db()->log()->count());

		$record->id1  = null;
		$record->save();
		self::assertEquals(0, Nano::db()->log()->count());

		$record->text = 'some other text';
		$record->save();
		self::assertEquals(0, Nano::db()->log()->count());

		$record->id1  = 2;
		$record->text = 'some text';
		$record->save();
		self::assertEquals(0, Nano::db()->log()->count());

		$record->id1  = 2;
		$record->text = 'some other text';
		$record->save();
		self::assertEquals(1, Nano::db()->log()->count());
		self::assertEquals("update `" . ActiveRecordCustomPk::TABLE_NAME . "` set `text` = 'some other text' where (id1 = '2' and id2 = '2')", Nano::db()->log()->getLastQuery());
	}

	public function testDelete() {
		$record = new ActiveRecordCustomPk(array(
			  'id1'  => 1
			, 'id2'  => 2
			, 'text' => 'some text'
		));
		$record->delete();

		self::assertEquals(1, Nano::db()->log()->count());
		self::assertEquals("delete from `" . ActiveRecordCustomPk::TABLE_NAME . "` where (id1 = '1' and id2 = '2')", Nano::db()->log()->getLastQuery());

		$record->id1 = null;
		$record->delete();
		self::assertEquals(2, Nano::db()->log()->count());
		self::assertEquals("delete from `" . ActiveRecordCustomPk::TABLE_NAME . "` where (id2 = '2' and text = 'some text')", Nano::db()->log()->getLastQuery());

		$record->id2 = null;
		$record->delete();
		self::assertEquals(3, Nano::db()->log()->count());
		self::assertEquals("delete from `" . ActiveRecordCustomPk::TABLE_NAME . "` where (text = 'some text')", Nano::db()->log()->getLastQuery());

		$record->text = null;
		$record->delete();
		self::assertEquals(4, Nano::db()->log()->count());
		self::assertEquals("delete from `" . ActiveRecordCustomPk::TABLE_NAME . "`", Nano::db()->log()->getLastQuery());
	}

	protected function tearDown() {
		ActiveRecordCustomPk::deleteTable();
		ActiveRecordBasic::deleteTable();
	}

}