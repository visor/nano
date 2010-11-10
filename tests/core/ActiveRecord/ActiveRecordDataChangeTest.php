<?php

/**
 * @group active-record
 * @group framework
 */
class ActiveRecordDataChangeTest extends TestUtils_TestCase {

	public static function setUpBeforeClass() {
		require_once __DIR__ . '/_files/ActiveRecordBasic.php';
	}

	public function testUnknownFields() {
		$runnable = function() {
			$test = new ActiveRecordBasic();
			$test->someField = 123;
		};
		self::assertException($runnable, 'ActiveRecord_Exception_UnknownField', 'Unknown field "someField" in class ActiveRecordBasic');

		$runnable = function() {
			$test = new ActiveRecordBasic();
			$test->anotherField = 123;
		};
		self::assertException($runnable, 'ActiveRecord_Exception_UnknownField', 'Unknown field "anotherField" in class ActiveRecordBasic');

		$runnable = function() {
			$test = new ActiveRecordBasic();
			$test->id   = 1;
			$test->text = '123';
		};
		self::assertNoException($runnable);
	}

	public function testEmptyDataChange() {
		$record = new ActiveRecordBasic();
		$record->id = 100;
		self::assertEquals(array('id' => 100), $record->getChangedData());

		$record = new ActiveRecordBasic();
		$record->text = 'some text';
		self::assertEquals(array('text' => 'some text'), $record->getChangedData());

		$record = new ActiveRecordBasic();
		$record->id   = 100;
		$record->text = 'some text';
		self::assertEquals(array('id' => 100, 'text' => 'some text'), $record->getChangedData());
	}

	public function testOneFieldChanged() {
		$record = new ActiveRecordBasic(array('id' => 1, 'text' => 'some text'));
		$record->text = 'another text';
		self::assertEquals(array('text' => 'another text'), $record->getChangedData());

		$record->text = 'yet another text';
		self::assertEquals(array('text' => 'yet another text'), $record->getChangedData());
	}

	public function testAllFieldsChanged() {
		$record = new ActiveRecordBasic(array('id' => 1, 'text' => 'some text'));
		$record->id   = 2;
		$record->text = 'another text';
		self::assertEquals(array('id' => 2, 'text' => 'another text'), $record->getChangedData());

		$record = new ActiveRecordBasic();
		$record->id   = 2;
		$record->text = 'another text';
		self::assertEquals(array('id' => 2, 'text' => 'another text'), $record->getChangedData());
	}

	public function testSettingSameValue() {
		$record = new ActiveRecordBasic(array('id' => 1, 'text' => 'some text'));
		$record->id = 1;
		self::assertEquals(array(), $record->getChangedData());
	}

	public function testUnsetting() {
		$record = new ActiveRecordBasic(array('id' => 1, 'text' => 'some text'));
		unset($record->id);
		self::assertEquals(array('id' => null), $record->getChangedData());
	}

}