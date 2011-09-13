<?php

/**
 * @group framework
 * @group orm
 */
class Library_Orm_MongoMapperTest extends TestUtils_TestCase {

	/***
	 * @var Orm_DataSource_Mongo
	 */
	protected $source;

	protected function setUp() {
		include_once $this->files->get($this, '/model/AddressMongo.php');
		include_once $this->files->get($this, '/mapper/AddressMongo.php');

		$this->source = new Orm_DataSource_Mongo(array(
			'server'     => 'localhost'
			, 'database' => 'nano_test'
		));
		Orm::instance()->addSource('test', $this->source);
	}

	public function testSavingNewModelIntoDataSource() {
		$address = new Library_Orm_Example_AddressMongo();
		$address->location = 'Number 4, Privet Drive';

		self::assertTrue($address->isNew());
		self::assertTrue($address->changed());
		self::assertTrue($address->save());
		self::assertNotNull($address->_id);
		self::assertInternalType('string', $address->_id);
		self::assertFalse($address->isNew());
		self::assertFalse($address->changed());
	}

	public function testSavingLoadedModelIntoDataSource() {
		$data = array('location' => 'Number 4, Privet Drive');
		$new  = 'Number 4, Privet Drive, Little Whinging';
		$this->source->db()->selectCollection('address')->insert($data);

		$address = Library_Orm_Example_AddressMongo::mapper()->get($data['_id']);
		self::assertEquals($data['location'], $address->location);
		self::assertFalse($address->isNew());
		self::assertNotNull($address->_id);
		self::assertInternalType('string', $address->_id);

		$address->location = $new;
		self::assertTrue($address->changed());
		self::assertTrue($address->save());
		self::assertFalse($address->isNew());
		self::assertFalse($address->changed());
	}

	public function testDeletingModel() {
		$data = array('location' => 'Number 4, Privet Drive');
		$this->source->db()->selectCollection('address')->insert($data);

		self::assertEquals(1, $this->source->db()->selectCollection('address')->count());
		self::assertTrue(Library_Orm_Example_AddressMongo::mapper()->get($data['_id'])->delete());
		self::assertEquals(0, $this->source->db()->selectCollection('address')->count());
	}

	public function testFindShoudlReturnFalseWhenException() {
		self::assertFalse(Library_Orm_Example_AddressMongo::mapper()->find(Orm::criteria()->equals('invalid', 'some')));
	}

	protected function tearDown() {
		$this->source->db()->drop();
		$this->source = null;
	}

}