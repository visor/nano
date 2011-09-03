<?php

/**
 * @group framework
 * @group orm
 */
class Library_Orm_MysqlSourceTypesTest extends TestUtils_TestCase {

	/**
	 * @var Orm_DataSource_Mysql
	 */
	protected $source;

	/**
	 * @var Orm_Mapper
	 */
	protected $mapper;

	protected function setUp() {
		include_once $this->files->get($this, '/model/Address.php');

		$this->source = new Orm_DataSource_Pdo_Mysql(array());
	}

	public function testSupportedTypes() {
		$types = self::getObjectProperty($this->source, 'supportedTypes');
		foreach ($types as $typeName => $className) {
			self::assertTrue($this->source->typeSupported($typeName), $typeName . ' should be supported');
			self::assertFalse($this->source->typeSupported($typeName . '_fails'), $typeName . '_fails should be not supported');
		}
	}

	public function testTypeInstanceShouldCreatedOnlyOnce() {
		$data = self::getObjectProperty($this->source, 'supportedTypes');
		foreach ($data as $name => $suffix) {
			$type = $this->source->type($name);
			self::assertSame($type, $this->source->type($name));
			self::assertSame($this->source->type($name), $this->source->type($name));
		}
	}

	public function testShouldThrowExceptionWhenRetrievingUnsupportedTypes() {
		$data = self::getObjectProperty($this->source, 'supportedTypes');
		foreach ($data as $name => $suffix) {
			$type   = $name . '-unsupported';
			$source = $this->source;
			self::assertException(
				function() use ($source, $type) {
					$source->type($type);
				}
				, 'Orm_Exception_UnsupportedType'
				, 'Unsupported type: "' . $type . '"'
			);
		}
	}

	public function testCastingMysqlScalarFields() {
		$values = array(
			'string'    => array('string value', 'string value')
			, 'double'  => array(10.01, 10.01)
			, 'integer' => array(10, 10)
			, 'boolean' => array(true, 1)
		);
		foreach ($values as $type => $value) {
			list($modelValue, $sourceValue) = $value;
			self::assertEquals($modelValue, $this->source->type($type)->castToModel($sourceValue));
			self::assertEquals($sourceValue, $this->source->type($type)->castToDataSource($modelValue));
		}
	}

	public function testCastingMysqlDateField() {
		Nano_Log::message(__FUNCTION__);
		Nano_Log::message(var_export($this->source, true));
		$sourceValue = '2010-01-01';
		$modelValue  = Date::create($sourceValue);
		$this->castingFieldType('string', $sourceValue, $modelValue, 'date');
	}

	public function testCastingMysqlDateTimeField() {
		$sourceValue = '2010-01-01 12:01:02';
		$modelValue  = Date::create($sourceValue);
		$this->castingFieldType('string', $sourceValue, $modelValue, 'datetime');
	}

	public function testCastingMysqlTimestampField() {
		$sourceValue = '20100101120102';
		$modelValue  = Date::create('2010-01-01 12:01:02');
		$this->castingFieldType('string', $sourceValue, $modelValue, 'timestamp');
	}

	protected function tearDown() {
		$this->source = null;
		$this->mapper = null;
	}

	protected function castingFieldType($typeClass, $sourceValue, $modelValue, $typeName) {
		Nano_Log::message(var_export($this->source->type($typeName), true));
		self::assertEquals($modelValue, $this->source->type($typeName)->castToModel($sourceValue), 'Model value should equals');
		self::assertInternalType($typeClass, $this->source->type($typeName)->castToDataSource($modelValue), 'Internal types should equals');
		self::assertEquals($sourceValue, $this->source->type($typeName)->castToDataSource($modelValue), 'Source values should equals');
	}

}