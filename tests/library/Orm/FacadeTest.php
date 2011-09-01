<?php

/**
 * @group framework
 * @group orm
 */
class Library_Orm_FacadeTest extends TestUtils_TestCase {

	private $backup;

	private $dataSource;

	protected function setUp() {
		require_once $this->files->get($this, '/TestDataSource.php');

		$this->backup     = Orm::backup();
		$this->dataSource = new Library_Orm_TestDataSource(array());
		Orm::instance()->addSource('test-1', $this->dataSource);
	}

	public function testBackingUpDataSources() {
		$backup = Orm::backup();
		self::assertEquals(array('test-1' => $this->dataSource), $backup);
		self::assertTrue(Orm::restore($backup));
	}

	public function testSettingupDataSources() {
		self::assertSame($this->dataSource, Orm::instance()->source('test-1'));

		$source2 = new Library_Orm_TestDataSource(array());
		Orm::instance()->addSource('test-2', $source2);
		self::assertNotSame($this->dataSource, Orm::instance()->source('test-2'));
		self::assertNotSame($source2, Orm::instance()->source('test-1'));
		self::assertSame($source2, Orm::instance()->source('test-2'));
	}

	public function testShouldThrowExceptionWhenDataSourceNotExists() {
		$this->setExpectedException('Orm_Exception_InvalidDataSource', 'Invalid DataSource: \'test-2\'');
		Orm::instance()->source('test-2');
	}

	public function testShouldReturnFalseWhenInvalidSourcesPassedForRestore() {
		$backup = array(
			'test-1'   => $this->dataSource
			, 'test-2' => null
		);
		self::assertFalse(Orm::restore($backup));
	}

	protected function tearDown() {
		Orm::restore($this->backup);
		$this->dataSource = null;
	}

}