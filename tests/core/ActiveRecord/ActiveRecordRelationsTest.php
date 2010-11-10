<?php

/**
 * @group active-record
 */
class ActiveRecordRelationsTest extends TestUtils_TestCase {

	/**
	 * @var array
	 */
	protected $childs  = array();

	public static function setUpBeforeClass() {
		require_once __DIR__ . '/_files/ActiveRecordBasic.php';
		require_once __DIR__ . '/_files/ActiveRecordChild.php';
		ActiveRecordBasic::deleteTable();
		ActiveRecordChild::deleteTable();
		ActiveRecordBasic::createTable();
		ActiveRecordChild::createTable();
		Nano_Log::clear();
	}

	protected function setUp() {
		Nano::db()->beginTransaction();
		for ($i = 1; $i < 5; ++$i) {
			$parent       = ActiveRecordBasic::instance();
			$parent->text = 'parent ' . $i;
			$parent->save();

			$child = ActiveRecordChild::instance();
			$child->parent_id = $parent->id;
			$child->text      = 'child ' . $i;
			$child->save();

			$this->childs[$child->id] = $parent->id;
		}
	}

	public function testRelationExists() {
		self::assertTrue(ActiveRecordChild::prototype()->relationExists('parent'));
		self::assertTrue(ActiveRecordChild::prototype()->relationExists('pArent'));
		self::assertTrue(ActiveRecordChild::prototype()->relationExists('Parent'));
		self::assertFalse(ActiveRecordChild::prototype()->relationExists('parent_id'));
	}

	public function testRelationQuery() {
		$record     = ActiveRecordChild::instance();
		$simple     = 'select `active_record_child`.* from `active_record_child`';
//		$withParent =
//			'select'
//				. ' `active_record_child`.*'
//				. ', `active_record_test`.id as ' . Nano::db()->quote('parent::id')
//				. ', `active_record_test`.text as ' . Nano::db()->quote('parent::text')
//			. ' from'
//				. ' `active_record_child`'
//				. ' inner join `active_record_test` on (`active_record_child`.parent_id = `active_record_test`.id)'
//		;

		self::assertEquals($simple, ActiveRecord_Storage::getSelectQuery($record)->toString());

//		$record->parent_id = 1;
//		self::assertEquals($withParent, ActiveRecord_Storage::getSelectQuery($record)->toString());
	}

	public function testShouldThrowExceptionForUnknownRelation() {
		self::assertException(
			function () {
				ActiveRecord_Storage::getRelatedRecord(ActiveRecordChild::instance(), 'child');
			}
			, 'ActiveRecord_Exception_UnknownRelation'
			, 'Unknown relation "child" in class ActiveRecordChild'
		);
	}

	public function testShouldThrowExceptionForNotExistedRelationTarge() {
		self::assertException(
			function () {
				$child = ActiveRecordChild::instance();
				$child->parent_id = 'some';
				ActiveRecord_Storage::getRelatedRecord($child, 'parent');
			}
			, 'ActiveRecord_Exception_RelationTargetNotFound'
			, 'Required relation target ActiveRecordBasic not found for ActiveRecordChild'
		);
	}

	public function testLoadingOne() {
		$record = ActiveRecordChild::instance();
		self::assertType('ActiveRecordBasic', $record->parent);
		self::assertTrue($record->parent->isNew());

		foreach ($this->childs as $id => $parentId) {
			$parent = ActiveRecordBasic::instance()->findOne($parentId);
			$child  = ActiveRecordChild::instance()->findOne($id);
			self::assertType('ActiveRecordBasic', $parent);
			self::assertType('ActiveRecordChild', $child);
			self::assertType('ActiveRecordBasic', $child->parent);
			self::assertEquals($parentId, $child->parent->id);
		}
	}

	public function testSettingOne() {
		$record = ActiveRecordChild::instance();
		self::assertType('ActiveRecordBasic', $record->parent);
		self::assertTrue($record->parent->isNew());

		$record->parent->save();
		self::assertFalse($record->parent->isNew());

		$record->parent = ActiveRecordBasic::instance();
		self::assertTrue($record->parent->isNew());
	}

	protected function tearDown() {
		Nano::db()->rollBack();
	}

	public static function tearDownAfterClass() {
		ActiveRecordChild::deleteTable();
		ActiveRecordBasic::deleteTable();
	}

}