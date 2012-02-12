<?php

/**
 * @group library
 * @group orm
 */
class Library_Orm_CommonTypesTest extends TestUtils_TestCase {

	/**
	 * @var Library_Orm_TestDataSource
	 */
	protected $source;

	protected function setUp() {
		include_once $this->files->get($this, '/TestDataSource.php');
		$this->source = new Library_Orm_TestDataSource(array());
	}

	/**
	 * @return array
	 */
	public function getSourceTypes() {
		include_once $this->files->get($this, '/TestDataSource.php');
		$result = array();
		foreach (self::getObjectProperty(new Library_Orm_TestDataSource(array()), 'supportedTypes') as $typeName => $className) {
			$result[] = array($typeName);
		}
		return $result;
	}

	/**
	 * @dataProvider getSourceTypes()
	 * @param stirng $type
	 */
	public function testGettingUnsupportedTypesShouldThrowException($type) {
		$this->setExpectedException('Orm_Exception_UnsupportedType', 'Unsupported type: "' . $type . '-unsupported"');
		Orm_Types::getType($this->source, $type . '-unsupported');
	}

	public function testCastingBooleanToModel() {
		$data = array(
			'0'   => false
			, ''  => false
			, '1' => true
			, 0   => false
			, 1   => true
		);
		foreach ($data as $value => $expected) {
			self::assertEquals($expected, Orm_Types::getType($this->source, 'boolean')->castToModel($value), var_export($value, true) . ' should be ' . var_export($expected, true));
			self::assertInternalType('boolean', Orm_Types::getType($this->source, 'boolean')->castToModel($value), var_export($value, true) . ' should be boolean');
		}
	}

	public function testCastingStringToModel() {
		$data = array(
			'100500'                    => 100500
			, 'Array'                   => array()
			, 'string'                  => 'string'
			, '2000-01-01T01:01:01+0000' => Date::create('2000-01-01T01:01:01+0000')
			, '1'                       => true
			, ''                        => false
			, '100.5'                   => 100.5
		);
		foreach ($data as $expected => $value) {
			self::assertEquals($expected, Orm_Types::getType($this->source, 'string')->castToModel($value), var_export($value, true) . ' should be ' . var_export($expected, true));
			self::assertInternalType('string', Orm_Types::getType($this->source, 'string')->castToModel($value), var_export($value, true) . ' should be string');
		}
	}

	public function testCastingIntegerToModel() {
		$data = array(
			'100500'                    => 100500
			, 'Array'                   => 0
			, 'string'                  => 0
			, '2000-01-01T01:01:01+000' => 2000
			, '1'                       => 1
			, '0'                       => 0
			, '100.1'                   => 100
			, '100.9'                   => 100
		);
		foreach ($data as $value => $expected) {
			self::assertEquals($expected, Orm_Types::getType($this->source, 'integer')->castToModel($value), var_export($value, true) . ' should be ' . var_export($expected, true));
			self::assertInternalType('int', Orm_Types::getType($this->source, 'integer')->castToModel($value), var_export($value, true) . ' should be integer');
		}
	}

	public function testCastingDoubleToModel() {
		$data = array(
			'100500'                    => 100500.0
			, 'Array'                   => 0.0
			, 'string'                  => 0.0
			, '2000-01-01T01:01:01+000' => 2000.0
			, '1'                       => 1
			, '0'                       => 0
			, '100.1'                   => 100.1
			, '100.9'                   => 100.9
		);
		foreach ($data as $value => $expected) {
			self::assertEquals($expected, Orm_Types::getType($this->source, 'double')->castToModel($value), var_export($value, true) . ' should be ' . var_export($expected, true));
			self::assertInternalType('float', Orm_Types::getType($this->source, 'double')->castToModel($value), var_export($value, true) . ' should be double');
		}
	}

	protected function tearDown() {
		$this->source = null;
		self::setObjectProperty('Orm_Types', 'types', array());
	}

}