<?php

/**
 * @group active-record
 * @group library
 */
class Library_ActiveRecord_ErrorsTest extends TestUtils_TestCase {

	public function testNoName() {
		$this->setExpectedException('ActiveRecord_Exception_NoTableName', 'Table name is not specified for class ActiveRecordNoName');
		require __DIR__ . '/_files/ActiveRecordNoName.php';
		new ActiveRecordNoName();
	}

	public function testNoPrimaryKey() {
		$this->setExpectedException('ActiveRecord_Exception_NoPrimaryKey', 'Primary key is not specified for class ActiveRecordNoPrimaryKey');

		require __DIR__ . '/_files/ActiveRecordNoPrimaryKey.php';
		new ActiveRecordNoPrimaryKey();
	}

	public function testNoAutoIncrement() {
		$this->setExpectedException('ActiveRecord_Exception_AutoIncrementNotDefined', 'Autoincrement flag not defined for class ActiveRecordNoAutoIncrement');

		require __DIR__ . '/_files/ActiveRecordNoAutoIncrement.php';
		new ActiveRecordNoAutoIncrement();
	}

	public function testNoFields() {
		$this->setExpectedException('ActiveRecord_Exception_NoFields', 'No fields defined for class ActiveRecordNoFields');

		require __DIR__ . '/_files/ActiveRecordNoFields.php';
		new ActiveRecordNoFields();
	}

	public function testInvalidConstructorData() {
		$this->setExpectedException('InvalidArgumentException', '');

		require_once __DIR__ . '/_files/ActiveRecordBasic.php';
		new ActiveRecordBasic('some value');
	}

	public function testReadUnknownFields() {
		$this->setExpectedException('ActiveRecord_Exception_UnknownField', 'Unknown field "someField"');

		require_once __DIR__ . '/_files/ActiveRecordBasic.php';
		$x = ActiveRecordBasic::prototype()->someField;
	}

	public function testSetUnknownFields() {
		$this->setExpectedException('ActiveRecord_Exception_UnknownField', 'Unknown field "anotherField"');

		require_once __DIR__ . '/_files/ActiveRecordBasic.php';
		ActiveRecordBasic::prototype()->anotherField = 'some value';
	}

}