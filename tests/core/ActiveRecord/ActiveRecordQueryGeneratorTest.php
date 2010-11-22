<?php

/**
 * @group active-record
 * @group framework
 */
class ActiveRecordQueryGeneratorTest extends TestUtils_TestCase {

	public static function setUpBeforeClass() {
		require_once __DIR__ . '/_files/ActiveRecordBasic.php';
		require_once __DIR__ . '/_files/ActiveRecordCustomPk.php';
	}

	public function testDetectQueryType() {
		$record = new ActiveRecordBasic();
		self::assertTrue($record->isNew());

		$record = new ActiveRecordBasic(array('id' => 1), true);
		self::assertFalse($record->isNew());
	}

	public function testGeneratingWhereCondition() {
		$record = ActiveRecordBasic::instance();
		self::assertEquals("(`id1` > '10')", $record->getSelectCriteria(array('id1' => '>10'))->toString());
		self::assertEquals("(`id1` < '10')", $record->getSelectCriteria(array('id1' => '<10'))->toString());
		self::assertEquals("(`id1` != '10')", $record->getSelectCriteria(array('id1' => '!10'))->toString());
		self::assertEquals("(`id1` like '%10%')", $record->getSelectCriteria(array('id1' => '%%10%'))->toString());
		self::assertEquals("(`id1` = '10')", $record->getSelectCriteria(array('id1' => '=10'))->toString());
		self::assertEquals("(`id1` >= '10')", $record->getSelectCriteria(array('id1' => '>=10'))->toString());
		self::assertEquals("(`id1` <= '10')", $record->getSelectCriteria(array('id1' => '<=10'))->toString());
	}

	public function testDetecingPrimaryKeyValue() {
		$record = new ActiveRecordCustomPk();
		$record->id1 = 1;
		self::assertEquals(array('id1' => 1, 'id2' => null), $record->getPrimaryKey());

		$record = new ActiveRecordBasic();
		$record->id = 1;
		self::assertEquals(1, $record->getPrimaryKey());
		self::assertEquals(array('id' => 1), $record->getPrimaryKey(true));
	}

	public function testGenerateInsertData() {
		$record = new ActiveRecordBasic();
		self::assertEquals(array('text' => null), $record->getInsertFields());

		$record->text = 'some text';
		self::assertEquals(array('text' => $record->text), $record->getInsertFields());

		$record->id = 1001;
		self::assertEquals(array('text' => $record->text), $record->getInsertFields());

		$record->text = null;
		self::assertEquals(array('text' => $record->text), $record->getInsertFields());
	}

	public function testGenerateUpdateData() {
		$record = new ActiveRecordCustomPk();
		self::assertTrue($record->getDeleteCriteria()->isEmpty());
		self::assertEquals('', $record->getUpdateCriteria()->toString());
		self::assertEquals(array(), $record->getUpdateFields());

		$record->id1 = 10;
		self::assertTrue($record->getUpdateCriteria()->isEmpty());
		self::assertEquals('', $record->getUpdateCriteria()->toString());
		self::assertEquals(array(), $record->getUpdateFields());

		$record->id2 = 20;
		self::assertFalse($record->getUpdateCriteria()->isEmpty());
		self::assertEquals("(`id1` = '10' and `id2` = '20')", $record->getUpdateCriteria()->toString());
		self::assertEquals(array(), $record->getUpdateFields());

		$record->text = 'some text';
		self::assertFalse($record->getUpdateCriteria()->isEmpty());
		self::assertEquals("(`id1` = '10' and `id2` = '20')", $record->getUpdateCriteria()->toString());
		self::assertEquals(array('text' => $record->text), $record->getUpdateFields());

		$record->id2 = null;
		self::assertTrue($record->getUpdateCriteria()->isEmpty());
		self::assertEquals('', $record->getUpdateCriteria()->toString());
		self::assertEquals(array('text' => $record->text), $record->getUpdateFields());

		$record->id1 = null;
		self::assertTrue($record->getUpdateCriteria()->isEmpty());
		self::assertEquals('', $record->getUpdateCriteria()->toString());
		self::assertEquals(array('text' => $record->text), $record->getUpdateFields());
	}

	public function testGenerateDeleteData() {
		$record = new ActiveRecordCustomPk();
		self::assertTrue($record->getDeleteCriteria()->isEmpty());
		self::assertEquals('', $record->getDeleteCriteria()->toString());

		$record->id1 = 10;
		self::assertFalse($record->getDeleteCriteria()->isEmpty());
		self::assertEquals("(`id1` = '10')", $record->getDeleteCriteria()->toString());

		$record->id2 = 20;
		self::assertFalse($record->getDeleteCriteria()->isEmpty());
		self::assertEquals("(`id1` = '10' and `id2` = '20')", $record->getDeleteCriteria()->toString());

		$record->text = 'some text';
		self::assertFalse($record->getDeleteCriteria()->isEmpty());
		self::assertEquals("(`id1` = '10' and `id2` = '20')", $record->getDeleteCriteria()->toString());

		$record->id2 = null;
		self::assertFalse($record->getDeleteCriteria()->isEmpty());
		self::assertEquals("(`id1` = '10' and `text` = 'some text')", $record->getDeleteCriteria()->toString());

		$record->id1 = null;
		self::assertFalse($record->getDeleteCriteria()->isEmpty());
		self::assertEquals("(`text` = 'some text')", $record->getDeleteCriteria()->toString());
	}

}
