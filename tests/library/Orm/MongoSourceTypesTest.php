<?php

/**
 * @group framework
 * @group orm
 */
class Library_Orm_MongoSourceTypesTest extends TestUtils_TestCase {

	/**
	 * @var Orm_DataSource_Mongo
	 */
	protected $source;

	protected function setUp() {
		include_once $this->files->get($this, '/model/AddressMongo.php');

		$this->source = new Orm_DataSource_Mongo(array(
			'server'     => 'localhost'
			, 'database' => 'nano_test'
		));
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

	public function testCastingMongoIdFields() {
		$modelValue  = '100000000000000000000001';
		$sourceValue = new MongoId($modelValue);
		$this->castingMondoFieldType('MongoId', $sourceValue, $modelValue, 'identify');
	}

	public function testCastingMongoDateFields() {
		$modelValue  = Date::create('2010-10-10 12:00:00');
		$sourceValue = new MongoDate($modelValue->getTimestamp());
		$this->castingMondoFieldType('MongoDate', $sourceValue, $modelValue, 'date');
	}

	public function testCastingMongoBinaryFields() {
		$modelValue  = 'some value';
		$sourceValue = new MongoBinData($modelValue);
		$this->castingMondoFieldType('MongoBinData', $sourceValue, $modelValue, 'binary');
	}

	public function testCastingMongoArrayFields() {
		$value = array(1, 2, 3);
		self::assertEquals($value, $this->source->type('array')->castToModel($value));
		self::assertEquals($value, $this->source->type('array')->castToDataSource($value));
	}

	public function testCastingMongoObjectFields() {
		$sourceValue = array('field' => 'value');
		$modelValue = (object)$sourceValue;

		self::assertEquals($modelValue, $this->source->type('object')->castToModel($sourceValue));
		self::assertEquals($sourceValue, $this->source->type('object')->castToDataSource($modelValue));
	}

	public function testCastingMongoReferenceFields() {
		self::markTestIncomplete('Not implemented yet');
	}

	public function testCastingMongoScalarFields() {
		$values = array(
			'string'    => 'string value'
			, 'double'  => 10.01
			, 'integer' => 10
			, 'boolean' => true
		);
		foreach ($values as $type => $value) {
			self::assertEquals($value, $this->source->type($type)->castToModel($value));
			self::assertEquals($value, $this->source->type($type)->castToDataSource($value));
		}
	}

	protected function tearDown() {
		$this->source->db()->drop();
		$this->source = null;
	}

	protected function castingMondoFieldType($mongoTypeClass, $sourceValue, $modelValue, $typeName) {
		self::assertEquals($modelValue, $this->source->type($typeName)->castToModel($sourceValue));
		self::assertInstanceOf($mongoTypeClass, $this->source->type($typeName)->castToDataSource($modelValue));
		self::assertEquals($sourceValue, $this->source->type($typeName)->castToDataSource($modelValue));
	}

}