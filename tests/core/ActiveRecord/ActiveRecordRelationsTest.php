<?php

/**
 * @group active-record
 * @group framework
 */
class ActiveRecordRelationsTest extends TestUtils_TestCase {

	/**
	 * @var array
	 */
	protected $childs  = array();

	public static function setUpBeforeClass() {
		require_once __DIR__ . '/_files/ActiveRecordBasic.php';
		require_once __DIR__ . '/_files/ActiveRecordChild.php';
		require_once __DIR__ . '/_files/ActiveRecordSimple.php';
		require_once __DIR__ . '/_files/ActiveRecordExtendedChild.php';
		ActiveRecordBasic::deleteTable();
		ActiveRecordChild::deleteTable();
		ActiveRecordBasic::createTable();
		ActiveRecordChild::createTable();
		ActiveRecordSimple::deleteTable();
		ActiveRecordSimple::createTable();
		ActiveRecordExtendedChild::deleteTable();
		ActiveRecordExtendedChild::createTable();
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
		$record = ActiveRecordChild::instance();
		$simple = 'select `active_record_child`.* from `active_record_child`';
		self::assertEquals($simple, ActiveRecord_Storage::getSelectQuery($record)->toString());
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
		self::assertInstanceOf('ActiveRecordBasic', $record->parent);
		self::assertTrue($record->parent->isNew());

		foreach ($this->childs as $id => $parentId) {
			$parent = ActiveRecordBasic::instance()->findOne($parentId);
			$child  = ActiveRecordChild::instance()->findOne($id);
			self::assertInstanceOf('ActiveRecordBasic', $parent);
			self::assertInstanceOf('ActiveRecordChild', $child);
			self::assertInstanceOf('ActiveRecordBasic', $child->parent);
			self::assertEquals($parentId, $child->parent->id);
		}
	}

	public function testGetShouldReturnSameNewParentForOneChild() {
		$record = ActiveRecordChild::instance();
		$firstParent = $record->parent;
		self::assertInstanceOf('ActiveRecordBasic', $record->parent);
		self::assertInstanceOf('ActiveRecordBasic', $firstParent);
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
		self::assertInstanceOf('ActiveRecordBasic', $record->parent);
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

	public function testSeveralOneToManyRelations() {
		$parent1 = ActiveRecordBasic::instance()->populate(array('text' => 'parent1'));
		$parent1->save();
		$parent2 = ActiveRecordSimple::instance()->populate(array('text' => 'parent2'));
		$parent2->save();
		$child   = ActiveRecordExtendedChild::instance()->populate(array(
			  'parent_id1' => $parent1->id
			, 'parent_id2' => $parent2->id
			, 'text'       => 'child record'
		));
		$child->save();

		self::assertInstanceOf('ActiveRecordBasic', $child->parent1);
		self::assertInstanceOf('ActiveRecordSimple', $child->parent2);

		$loaded = ActiveRecordExtendedChild::prototype()->findOne($child->id);
		self::assertInstanceOf('ActiveRecordBasic', $loaded->parent1);
		self::assertInstanceOf('ActiveRecordSimple', $loaded->parent2);
		self::assertEquals($child->parent2, $loaded->parent2);
	}

	protected function tearDown() {
		Nano::db()->rollBack();
	}

	public static function tearDownAfterClass() {
		ActiveRecordChild::deleteTable();
		ActiveRecordBasic::deleteTable();
		ActiveRecordSimple::deleteTable();
		ActiveRecordExtendedChild::deleteTable();
	}

}