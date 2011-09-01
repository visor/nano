<?php

/**
 * @group framework
 * @group orm
 */
class Library_Orm_PdoMapperTest extends TestUtils_TestCase {

	/***
	 * @var Orm_DataSource_Pdo
	 */
	protected $source;

	protected function setUp() {
		include_once $this->files->get($this, '/model/Address.php');
		include_once $this->files->get($this, '/mapper/Address.php');

		$this->source = new Orm_DataSource_Pdo_Mysql(array());
		$this->source->usePdo(Nano::db());
		Orm::instance()->addSource('test', $this->source);
		$this->source->pdo()->beginTransaction();
	}

	public function testSavingNewModelIntoDataSource() {
		$address = new Library_Orm_Example_Address();
		$address->location = 'Number 4, Privet Drive';

		self::assertTrue($address->isNew());
		self::assertTrue($address->changed());
		self::assertTrue($address->save());
		self::assertNotNull($address->id);
		self::assertInternalType('integer', $address->id);
		self::assertFalse($address->isNew());
		self::assertFalse($address->changed());
	}

	public function testSavingLoadedModelIntoDataSource() {
		$old  = 'Number 4, Privet Drive';
		$new  = 'Number 4, Privet Drive, Little Whinging';

		$this->source->pdo()->exec('insert into address(location) values (' . $this->source->pdo()->quote('Number 4, Privet Drive'). ')');
		$id = $this->source->pdo()->lastInsertId();

		$address = Library_Orm_Example_Address::mapper()->get($id);
		self::assertFalse($address->isNew());
		self::assertEquals($old, $address->location);
		self::assertNotNull($address->id);
		self::assertInternalType('integer', $address->id);

		$address->location = $new;
		self::assertTrue($address->changed());
		self::assertTrue($address->save());
		self::assertFalse($address->changed());
	}

	public function testDeletingModel() {
		$this->source->pdo()->exec('insert into address(location) values (' . $this->source->pdo()->quote('Number 4, Privet Drive'). ')');
		$id = $this->source->pdo()->lastInsertId();

		self::assertEquals(1, $this->source->pdo()->query('select count(*) from address')->fetchColumn(0));
		self::assertTrue(Library_Orm_Example_Address::mapper()->get($id)->delete());
		self::assertEquals(0, $this->source->pdo()->query('select count(*) from address')->fetchColumn(0));
	}

	public function testFindShoudlReturnFalseWhenException() {
		self::assertFalse(Library_Orm_Example_Address::mapper()->find(Orm::criteria()->equals('invalid', 'some')));
	}

	protected function tearDown() {
		$this->source->pdo()->rollBack();
		$this->source = null;
	}

}