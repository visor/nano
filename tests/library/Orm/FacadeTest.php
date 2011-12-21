<?php

/**
 * @group framework
 * @group orm
 */
class Library_Orm_FacadeTest extends TestUtils_TestCase {

	private $dataSource;

	protected function setUp() {
		require_once $this->files->get($this, '/TestDataSource.php');

		$this->dataSource = new Library_Orm_TestDataSource(array());
		Orm::clearSources();
		Orm::addSource('test-1', $this->dataSource);
	}

	public function testSettingUpDataSources() {
		self::assertSame($this->dataSource, Orm::getSource('test-1'));

		$source2 = new Library_Orm_TestDataSource(array());
		Orm::addSource('test-2', $source2);
		self::assertNotSame($this->dataSource, Orm::getSource('test-2'));
		self::assertNotSame($source2, Orm::getSource('test-1'));
		self::assertSame($source2, Orm::getSource('test-2'));
	}

	public function testShouldThrowExceptionWhenDataSourceNotExists() {
		$this->setExpectedException('Orm_Exception_InvalidDataSource', 'Invalid DataSource: \'unknown-data-source\'');
		Orm::getSource('unknown-data-source');
	}

	public function testShouldClearSourcesArrayAndDefaultSource() {
		Orm::setDefaultSource('test-1');
		Orm::setSourceFor('test-1', 'test');
		Orm::clearSources();

		self::assertEquals(array(), self::getObjectProperty('Orm', 'dataSources'));
		self::assertEquals(array(), self::getObjectProperty('Orm', 'resourcesSource'));
		self::assertNull(self::getObjectProperty('Orm', 'defaultSource'));
	}

	public function testShouldReturnDefaultSourceForResourceIfNotOneSpecified() {
		Orm::setDefaultSource('test-1');
		self::assertSame(Orm::getSource('test-1'), Orm::getSourceFor('Library_Orm_Example_Address'));
	}

	public function testShouldReturnSpecifedResourceWhenSpecified() {
		$source2 = new Library_Orm_TestDataSource(array());
		$source3 = new Library_Orm_TestDataSource(array());

		Orm::setDefaultSource('test-1');
		Orm::addSource('test-2', $source2);
		Orm::addSource('test-3', $source3);
		Orm::setSourceFor('LibraryOrmExampleHouse', 'test-2');
		Orm::setSourceFor(array('Library_OrmExampleWizard' => 'test-3'));

		self::assertSame($this->dataSource, Orm::getSourceFor('Library_Orm_Example_Address'));
		self::assertSame($source2, Orm::getSourceFor('LibraryOrmExampleHouse'));
		self::assertSame($source3, Orm::getSourceFor('Library_OrmExampleWizard'));
	}

	public function testShouldThrowExceptionWhenDefaultSourceNotSetButRequired() {
		$this->setExpectedException('Orm_Exception_NoDefaultDataSource', 'Default data source not specified but required');
		Orm::getSourceFor('Library_Orm_Example_Address');
	}

	protected function tearDown() {
		unSet($this->dataSource);
		Orm::clearSources();
	}

}