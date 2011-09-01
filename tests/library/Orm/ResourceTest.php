<?php

/**
 * @group framework
 * @group orm
 * @group paranoid
 */
class Library_Orm_ResourceTest extends TestUtils_TestCase {

	private $testMeta = array(
		'name'        => 'test-resource'
		, 'source'    => 'test-source'
		, 'fields'    => array(
			  'id'    => array('type' => 'int', 'readonly' => true)
			, 'text'  => array('type' => 'text', 'default' => 'default value')
			, 'value' => array('type' => 'text', 'readonly' => false)
		)
		, 'identity'  => array('id')
		, 'hasOne'   => array('2', '2', '2')
		, 'hasMany'   => array('1', '2', '3')
		, 'belongsTo' => array('3', '2', '1')
	);

	/**
	 * @var Orm_Resource
	 */
	private $resource;

	private $backup;

	protected function setUp() {
		require_once $this->files->get($this, '/TestDataSource.php');
		$this->resource = new Orm_Resource($this->testMeta);
		$this->backup   = Orm::backup();
	}

	public function testGettingResourceInformation() {
		self::assertEquals($this->testMeta['name'], $this->resource->name());
		self::assertEquals($this->testMeta['fields'], $this->resource->fields());
		self::assertEquals($this->testMeta['identity'], $this->resource->identity());
		self::assertEquals($this->testMeta['hasOne'], $this->resource->hasOne());
		self::assertEquals($this->testMeta['hasMany'], $this->resource->hasMany());
		self::assertEquals($this->testMeta['belongsTo'], $this->resource->belongsTo());

		self::assertEquals(array_keys($this->testMeta['fields']), $this->resource->fieldNames());
		self::assertEquals($this->testMeta['fields']['id'], $this->resource->field('id'));
		self::assertEquals($this->testMeta['fields']['text'], $this->resource->field('text'));

		self::assertTrue($this->resource->isReadOnly('id'));
		self::assertFalse($this->resource->isReadOnly('text'));
		self::assertFalse($this->resource->isReadOnly('value'));

		self::assertNull($this->resource->defaultValue('id'));
		self::assertEquals($this->testMeta['fields']['text']['default'], $this->resource->defaultValue('text'));
	}

	public function testGettingMetaForUnknownFieldShouldThrowException() {
		self::setExpectedException('Orm_Exception_UnknownField', 'Unknown resource field: test-resource.field');
		$this->resource->field('field');
	}

	public function testReadOnlyForUnknownFieldShouldThrowException() {
		self::setExpectedException('Orm_Exception_UnknownField', 'Unknown resource field: test-resource.field');
		$this->resource->isReadOnly('field');
	}

	public function testDefaultValueForUnknownFieldShouldThrowException() {
		self::setExpectedException('Orm_Exception_UnknownField', 'Unknown resource field: test-resource.field');
		$this->resource->defaultValue('field');
	}

	public function testGettingDataSourceInstance() {
		$dataSource = new Library_Orm_TestDataSource(array());
		Orm::instance()->addSource('test-source', $dataSource);

		self::assertInstanceOf('Orm_DataSource', $this->resource->source());
		self::assertInstanceOf('Library_Orm_TestDataSource', $this->resource->source());
		self::assertEquals($dataSource, $this->resource->source());
		self::assertSame($dataSource, $this->resource->source());
	}

	protected function tearDown() {
		$this->resource = null;
		if (null !== $this->backup) {
			Orm::restore($this->backup);
			$this->backup = null;
		}
	}

}