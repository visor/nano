<?php

/**
 * @group active-record
 * @group framework
 */
class ActiveRecordPrototypeTest extends TestUtils_TestCase {

	public static function setUpBeforeClass() {
		require_once __DIR__ . '/_files/ActiveRecordBasic.php';
		require_once __DIR__ . '/_files/ActiveRecordCustomPk.php';
	}

	public function testInstance() {
		self::assertNotSame(ActiveRecordBasic::instance(), ActiveRecordBasic::instance());
	}

	public function testPrototype() {
		self::assertSame(ActiveRecordBasic::prototype(), ActiveRecordBasic::prototype());
	}

	public function testRefs() {
		self::assertNotSame(ActiveRecordBasic::instance(), ActiveRecordBasic::prototype());
	}

}