<?php

/**
 * @group framework
 * @group orm
 * @group paranoid
 */
class Library_Orm_ResourceTest extends TestUtils_TestCase {

	private $testMeta = array(
		'name'        => 'test-resource'
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

	protected function setUp() {
		require_once $this->files->get($this, '/TestDataSource.php');
		Orm::clearSources();
		$this->resource = new Orm_Resource($this->testMeta);
	}

	public function testGettingResourceInformation() {
		self::assertEquals($this->testMeta['name'], $this->resource->name());
		self::assertEquals($this->testMeta['fields'], $this->resource->fields());
		self::assertEquals($this->testMeta['identity'], $this->resource->identity());

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

	protected function tearDown() {
		$this->resource = null;
		Orm::clearSources();
	}

}