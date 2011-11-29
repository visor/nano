<?php

/**
 * @group framework
 * @group orm
 */
class Library_Orm_RuntimeCacheTest extends TestUtils_TestCase {

	/**
	 * @var Orm_DataSource_Pdo
	 */
	protected $source;

	/**
	 * @var Mapper_Library_Orm_Example_Address
	 */
	protected $mapper;

	protected function setUp() {
		include_once $this->files->get($this, '/model/Address.php');
		include_once $this->files->get($this, '/mapper/Address.php');

		$this->source = new Orm_DataSource_Pdo_Mysql(array());
		$this->source->usePdo(Nano::db());
		Orm::instance()->addSource('test', $this->source);
		$this->source->pdo()->beginTransaction();
		$this->mapper = Library_Orm_Example_Address::mapper();
	}

	public function testGetShouldReturnSameResultsForSameIdetities() {
		Nano::db()->insert($this->mapper->getResource()->name(), array('location' => 'Number 4, Privet Drive'));
		$id = (int)Nano::db()->lastInsertId();

		$model1 = $this->mapper->get($id);
		$model2 = $this->mapper->get($id);

		self::assertInstanceOf('Library_Orm_Example_Address', $model1);
		self::assertInstanceOf('Library_Orm_Example_Address', $model2);
		self::assertSame($model1, $model2);
	}

	public function testAfterInsertingNewRecordGetShouldReturnItWhenRequested() {
		$model1 = new Library_Orm_Example_Address();
		$model1->location = 'Number 4, Privet Drive';
		$model1->save();

		$model2 = $this->mapper->get($model1->id);
		self::assertInstanceOf('Library_Orm_Example_Address', $model2);
		self::assertSame($model1, $model2);
	}

	public function testFindShouldStoreModelsIntoCache() {
		Nano::db()->insert($this->mapper->getResource()->name(), array('location' => 'Number 4, Privet Drive'));
		$id1 = Nano::db()->lastInsertId();
		Nano::db()->insert($this->mapper->getResource()->name(), array('location' => 'The Burrow'));
		$id2 = Nano::db()->lastInsertId();
		Nano::db()->insert($this->mapper->getResource()->name(), array('location' => 'Game Hut at Hogwarts'));
		$id3 = Nano::db()->lastInsertId();

		$models = $this->mapper->find();
		self::assertEquals(3, count($models));
		foreach ($models as /** @var Orm_Model $model */ $model) {
			self::assertSame($model, $this->mapper->get($model->identity()));
		}
	}

	protected function tearDown() {
		$this->source->pdo()->rollBack();
		unSet($this->source, $this->mapper);
	}

}