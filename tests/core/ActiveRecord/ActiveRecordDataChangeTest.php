<?php

/**
 * @group active-record
 * @group framework
 */
class ActiveRecordDataChangeTest extends TestUtils_TestCase {

	public static function setUpBeforeClass() {
		require_once __DIR__ . '/_files/ActiveRecordBasic.php';
	}

	public function testShouldThrowExceptionWhenUnknownFieldAccessed() {
		$this->setExpectedException('ActiveRecord_Exception_UnknownField', 'Unknown field "someField" in class ActiveRecordBasic');
		$test = new ActiveRecordBasic();
		$test->someField = 123;
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

	public function testPopulating() {
		$record = new ActiveRecordBasic();
		$data   = array('id' => 1, 'text' => 'some text');
		$record->populate($data);
		self::assertEquals($data, $record->getChangedData());
		foreach ($data as $name => $value) {
			self::assertEquals($value, $record->__get($name), $name . ' should be ' . $value);
		}
	}

}