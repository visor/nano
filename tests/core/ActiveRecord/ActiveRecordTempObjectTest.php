<?php

/**
 * @group framework
 */
class ActiveRecordTempObjectTest extends TestUtils_TestCase {

	public static function setUpBeforeClass() {
		require_once __DIR__ . '/_files/ActiveRecordBasic.php';
		require_once __DIR__ . '/_files/ActiveRecordCustomPk.php';
		ActiveRecordCustomPk::deleteTable();
		ActiveRecordCustomPk::createTable();
		ActiveRecordBasic::deleteTable();
		ActiveRecordBasic::createTable();
	}

	protected function setUp() {
		$_SESSION = array();
	}

	public function testNewTempObjectShouldPlasedIntoSession() {
		$temp = new ActiveRecord_TempObject(ActiveRecordCustomPk::instance());
		self::assertArrayHasKey(ActiveRecord_TempObject::STORAGE_NAME, $_SESSION);
		self::assertSame($temp, $_SESSION[ActiveRecord_TempObject::STORAGE_NAME][ActiveRecord_TempObject::KEY_RECORD][$temp->id()]);
	}

	public function testPassingChilds() {
		$temp = new ActiveRecord_TempObject(ActiveRecordCustomPk::instance());
		self::assertNoException(function () use ($temp) {
			$temp->addChild(ActiveRecordBasic::instance(), '1', '2');
		});
		self::assertNoException(function () use ($temp) {
			$temp->addChild(new ActiveRecord_TempObject(ActiveRecordBasic::instance()), '1', '2');
		});
		self::assertException(
			function () use ($temp) {
				$temp->addChild(null, '1', '2');
			}
			, 'InvalidArgumentException'
			, ''
		);
		self::assertException(
			function () use ($temp) {
				$temp->addChild(new stdClass, '1', '2');
			}
			, 'InvalidArgumentException'
			, 'Child should be instance of ActiveRecord_TempObject or ActiveRecord'
		);
	}

	public function testAddingChilds() {
		$temp = new ActiveRecord_TempObject(ActiveRecordCustomPk::instance());
		$child1 = ActiveRecordBasic::instance();
		$child1->text = 'child for id1';
		$child2 = ActiveRecordBasic::instance();
		$child2->text = 'child for id2';
		$temp->addChild($child1, 'id', 'id1');
		$temp->addChild($child2, 'id', 'id2');

		$stored = $_SESSION[ActiveRecord_TempObject::STORAGE_NAME][ActiveRecord_TempObject::KEY_RECORD][$temp->id()];
		$childs = self::getObjectProperty($temp, 'childs');
		self::assertSame($temp, $stored);
		self::assertEquals($childs, self::getObjectProperty($stored, 'childs'));
		self::assertEquals(2, count($childs));
		self::assertSame($childs[0][ActiveRecord_TempObject::KEY_RECORD], $child1);
		self::assertSame($childs[1][ActiveRecord_TempObject::KEY_RECORD], $child2);
		return $temp;
	}

	/**
	 * @depends testAddingChilds
	 */
	public function testTempObjectShouldSleepAndWakeupCorrectly() {
		$temp = $this->testAddingChilds();
		$_SESSION = unSerialize(serialize($_SESSION));
		self::assertArrayHasKey(ActiveRecord_TempObject::STORAGE_NAME, $_SESSION);
		self::assertArrayHasKey(ActiveRecord_TempObject::KEY_RECORD, $_SESSION[ActiveRecord_TempObject::STORAGE_NAME]);
		self::assertArrayHasKey($temp->id(), $_SESSION[ActiveRecord_TempObject::STORAGE_NAME][ActiveRecord_TempObject::KEY_RECORD]);
		self::assertEquals($temp, $_SESSION[ActiveRecord_TempObject::STORAGE_NAME][ActiveRecord_TempObject::KEY_RECORD][$temp->id()]);
	}

	/**
	 * @depends testAddingChilds
	 */
	public function testRestoringFromSession() {
		$temp = $this->testAddingChilds();
		self::assertEquals($temp, ActiveRecord_TempObject::get($temp->id()));
		self::assertNull(ActiveRecord_TempObject::get($temp->id() + 1));
		self::assertNull(ActiveRecord_TempObject::get('some string'));
	}

	public function testSavingChildActiverRecords() {
		$temp1 = new ActiveRecord_TempObject(ActiveRecordBasic::instance());
		$temp2 = new ActiveRecord_TempObject(ActiveRecordBasic::instance());

		$child = ActiveRecordCustomPk::instance();
		$child->text = 'two links required';

		$temp1->addChild($child, 'id1', 'id');
		$temp2->addChild(new ActiveRecord_TempObject($child), 'id2', 'id');

		self::assertNoException(function () use ($temp1) {
			$temp1->save();
		});
		self::assertFalse($temp1->record()->isNew());
		self::assertNotNull($temp1->record()->getPrimaryKey(false));
		self::assertTrue($child->isNew());

		self::assertNoException(function () use ($temp2) {
			$temp2->save();
		});
		self::assertFalse($temp2->record()->isNew());
		self::assertNotNull($temp2->record()->getPrimaryKey(false));
		self::assertFalse($child->isNew());

		self::assertEquals(array('id1' => $temp1->record()->id, 'id2' => $temp2->record()->id), $child->getPrimaryKey(true));
	}

	protected function tearDown() {
		$_SESSION = array();
	}

	public static function tearDownAfterClass() {
		ActiveRecordCustomPk::deleteTable();
		ActiveRecordBasic::deleteTable();
	}

}