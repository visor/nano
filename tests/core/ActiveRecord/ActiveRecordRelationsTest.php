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
				ActiveRecord_Relation::getRecord(ActiveRecordChild::instance(), 'child');
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
				ActiveRecord_Relation::getRecord($child, 'parent');
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

	public function testGetShouldReturnSameNewParentForOneChild() {
		$record = ActiveRecordChild::instance();
		$firstParent = $record->parent;
		self::assertType('ActiveRecordBasic', $record->parent);
		self::assertType('ActiveRecordBasic', $firstParent);
		self::assertSame($firstParent, $record->parent);
	}

	public function testSettingOne() {
		$record         = ActiveRecordChild::instance();
		$newParent      = ActiveRecordBasic::instance();
		$record->parent = $newParent;
		self::assertSame($newParent, $record->parent);
		self::assertTrue($record->parent->isNew());
	}

	public function testShouldUpdateReferencesFiledWhenParentSaves() {
		$record = ActiveRecordChild::instance();
		self::assertType('ActiveRecordBasic', $record->parent);
		self::assertTrue($record->parent->isNew());

		$parent = $record->parent;
		$record->parent->save();
		self::assertFalse($record->parent->isNew());
		self::assertNotNull($record->parent->id);
		self::assertNotNull($record->parent_id);
		self::assertSame($record->parent, $parent);
		self::assertEquals($record->parent->id, $record->parent_id);
	}

	public function testShouldUseLastAssignedValueAsReferenceValue() {
		$record = ActiveRecordChild::instance();
		$parent1 = $record->parent;
		$parent1->text = 'first';
		$parent1->save();
		self::assertSame($record->parent, $parent1);

		$parent2 = ActiveRecordBasic::instance();
		$parent2->text = 'second';

		$record->parent = $parent2;
		self::assertNotSame($record->parent, $parent1);
		self::assertSame($record->parent, $parent2);

		$parent2->save();
		self::assertNotSame($record->parent, $parent1);
		self::assertSame($record->parent, $parent2);

		$parent1->id = $parent2->id + 100;
		$parent1->save();
		self::assertNotSame($record->parent, $parent1);
		self::assertSame($record->parent, $parent2);
	}

	public function testShouldUpdateChildRecordWhenPkChanged() {
		$record = ActiveRecordChild::instance();

		$parent = $record->parent;
		self::assertEquals($parent->id, $record->parent_id);
		self::assertEquals($record->parent->id, $record->parent_id);

		$parent->save();
		self::assertEquals($parent->id, $record->parent_id);
		self::assertEquals($record->parent->id, $record->parent_id);

		$parent->id = $parent->id + 10;
		self::assertEquals($parent->id, $record->parent_id);
		self::assertEquals($record->parent->id, $record->parent_id);
	}

	public function testShouldSaveRelatedRecordBeforeSelf() {
		$record = ActiveRecordChild::instance();
		$record->text = 'child';
		$record->parent->text = 'parent';
		self::assertNoException(function () use ($record) { $record->save(); });
		self::assertEquals($record->parent->id, $record->parent_id);
	}

	public function testShouldUpdatePrimaryKeyWhenReferencesFieldChanged() {
		self::markTestIncomplete('Not implemented yet');
	}

	protected function tearDown() {
		Nano::db()->rollBack();
	}

	public static function tearDownAfterClass() {
		ActiveRecordChild::deleteTable();
		ActiveRecordBasic::deleteTable();
	}

}