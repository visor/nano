<?php

/**
 * @group active-record
 */
class ActiveRecordErrorsTest extends TestUtils_TestCase {

	public function testNoName() {
		require __DIR__ . '/_files/ActiveRecordNoName.php';
		$runnable = function() {
			new ActiveRecordNoName();
		};
		self::assertException($runnable, 'ActiveRecord_Exception_NoTableName', 'Table name is not specified for class ActiveRecordNoName');
	}

	public function testNoPrimaryKey() {
		require __DIR__ . '/_files/ActiveRecordNoPrimaryKey.php';
		$runnable = function() {
			new ActiveRecordNoPrimaryKey();
		};
		self::assertException($runnable, 'ActiveRecord_Exception_NoPrimaryKey', 'Primary key is not specified for class ActiveRecordNoPrimaryKey');
	}

	public function testNoAutoIncrement() {
		require __DIR__ . '/_files/ActiveRecordNoAutoIncrement.php';
		$runnable = function() {
			new ActiveRecordNoAutoIncrement();
		};
		self::assertException($runnable, 'ActiveRecord_Exception_AutoIncrementNotDefined', 'Autoincrement flag not defined for class ActiveRecordNoAutoIncrement');
	}

	public function testNoFields() {
		require __DIR__ . '/_files/ActiveRecordNoFields.php';
		$runnable = function() {
			new ActiveRecordNoFields();
		};
		self::assertException($runnable, 'ActiveRecord_Exception_NoFields', 'No fields defined for class ActiveRecordNoFields');
	}

	public function testInvalidConstructorData() {
		require_once __DIR__ . '/_files/ActiveRecordBasic.php';
		self::assertException(function() { new ActiveRecordBasic('some value'); }, 'InvalidArgumentException', null);
	}

}