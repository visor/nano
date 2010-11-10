<?php

require_once dirName(__FILE__) . '/classes/TestDbTable1.php';
require_once dirName(__FILE__) . '/classes/TestDbTable2.php';

/**
 * @group framework
 */
class Nano_DbObjectTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Nano_Db
	 */
	protected $db;

	protected function setUp() {
		Nano_Db::clean();
		TestDbTable1::dropTable();
		TestDbTable2::dropTable();
		TestDbTable1::createTable();
		TestDbTable2::createTable();
		Nano::db()->delete(TestDbTable1::NAME);
		Nano::db()->delete(TestDbTable2::NAME);
	}

	public function testIsPrimaryKey() {
		$testObject1 = new TestDbTable1(array());

		$this->assertFalse($testObject1->isPrimaryKey(array()));
		$this->assertFalse($testObject1->isPrimaryKey(array('field1' => null)));
		$this->assertFalse($testObject1->isPrimaryKey(array('field2' => null)));

		$this->assertTrue($testObject1->isPrimaryKey(array('field2' => null, 'field1' => null)));
		$this->assertTrue($testObject1->isPrimaryKey(array('field1' => null, 'field2' => null)));
		$this->assertTrue($testObject1->isPrimaryKey(array('field1' => 1, 'field2' => 2)));

		$testObject2 = new TestDbTable2(array());
		$this->assertFalse($testObject2->isPrimaryKey(array()));
		$this->assertFalse($testObject2->isPrimaryKey(array('field2' => null)));

		$this->assertTrue($testObject2->isPrimaryKey(array('id' => null)));
		$this->assertTrue($testObject2->isPrimaryKey(2));
		$this->assertTrue($testObject2->isPrimaryKey('1'));
	}

	public function testLoadingById() {
		Nano::db()->insert(TestDbTable1::NAME, array(
			  'field1' => 1
			, 'field2' => 1
			, 'field3' => 'value 1'
			, 'field4' => 'value 2'
		));

		Nano::db()->insert(TestDbTable2::NAME, array(
			  'id'     => 10
			, 'field2' => 10
			, 'field3' => 'value 10'
			, 'field4' => 'value 20'
		));

		Nano::db()->insert(TestDbTable2::NAME, array(
			  'id'     => 20
			, 'field2' => 20
			, 'field3' => 'value 20'
			, 'field4' => 'value 22'
		));

		$object1 = new TestDbTable1(array('field1' => 1, 'field2' => 1));
		$this->assertFalse($object1->isNew());
		$this->assertEquals('value 1', $object1->field3);
		$this->assertEquals('value 2', $object1->field4);

		$object2 = new TestDbTable2(20);
		$this->assertFalse($object2->isNew());
		$this->assertEquals(20, $object2->field2);
		$this->assertEquals('value 20', $object2->field3);
		$this->assertEquals('value 22', $object2->field4);
	}

	public function testSave() {
		$object1 = TestDbTable1::createNew(2, 2);
		$this->assertTrue($object1->isNew());

		$object1->field3 = 'field3';
		$object1->field4 = 'field4';
		$object1->save();

		$this->assertFalse($object1->isNew());

		$object2 = new TestDbTable1(array('field1' => 2, 'field2' => 2));
		$this->assertFalse($object2->isNew());

		$this->assertEquals($object1->field1, $object2->field1);
		$this->assertEquals($object1->field2, $object2->field2);
		$this->assertEquals($object1->field3, $object2->field3);
		$this->assertEquals($object1->field4, $object2->field4);

		$object1->field4 = 'field4 modified';
		$object1->save();

		$object3 = new TestDbTable1(array('field1' => 2, 'field2' => 2));

		$this->assertFalse($object2->isNew());
		$this->assertEquals($object1->field1, $object3->field1);
		$this->assertEquals($object1->field2, $object3->field2);
		$this->assertEquals($object1->field3, $object3->field3);
		$this->assertEquals($object1->field4, $object3->field4);
	}

	public function testDelete() {
		Nano::db()->insert(TestDbTable1::NAME, array(
			  'field1' => 3
			, 'field2' => 3
			, 'field3' => 'value 3 1'
			, 'field4' => 'value 3 1'
		));

		$object1 = new TestDbTable1(array('field1' => 3, 'field2' => 3));
		$this->assertFalse($object1->isNew());
		$this->assertEquals('value 3 1', $object1->field3);
		$this->assertEquals('value 3 1', $object1->field4);

		$object1->delete();

		$object2 = new TestDbTable1(array('field1' => 3, 'field2' => 3));
		$this->assertTrue($object2->isNew());

		$row = Nano::db()->getRow(
			'select * from ' . TestDbTable1::NAME . ' where ' . Nano::db()->buildWhere(array(
				  'field1' => 3
				, 'field2' => 3
			))
		);
		$this->assertFalse($row);
	}

	protected function tearDown() {
		Nano_Db::clean();
		TestDbTable1::dropTable();
		TestDbTable2::dropTable();
	}

}