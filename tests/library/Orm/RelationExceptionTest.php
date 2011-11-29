<?php

/**
 * @group framework
 * @group orm
 */
class Library_Orm_RelationExceptionTest extends TestUtils_TestCase {

	/**
	 * @var Orm_DataSource_Pdo
	 */
	protected $source;

	/**
	 * @var Library_OrmExampleWizard
	 */
	protected $wizard;

	protected function setUp() {
		include_once $this->files->get($this, '/mapper/Wizard.php');
		include_once $this->files->get($this, '/model/Wizard.php');

		$this->source = new Orm_DataSource_Pdo_Mysql(array());
		$this->source->usePdo(Nano::db());
		$this->source->pdo()->beginTransaction();
		Orm::instance()->addSource('test', $this->source);

		$this->wizard = new Library_OrmExampleWizard();
	}

	public function testExceptionShouldThrowWhenNoTypeField() {
		$this->setExpectedException('Orm_Exception_IncompletedResource', 'Resource definition is not completed: wizard');
		$this->wizard->addressNoType;
	}

	public function testExceptionShouldThrowWhenUnknownTypeSpecified() {
		$this->setExpectedException('Orm_Exception_UnknownRelationType', 'Relation addressUnknownType with type some-relation-type is not supported');
		$this->wizard->addressUnknownType;
	}

	public function testExceptionShouldThrowWhenUnknownRelationGetted() {
		$this->setExpectedException('Orm_Exception_UnknownField', 'Unknown resource field: wizard.unknown relation');
		Library_OrmExampleWizard::mapper()->getResource()->getRelation('unknown relation');
	}

	protected function tearDown() {
		$this->source->pdo()->rollBack();
		unSet($this->source);
	}

}