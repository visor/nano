<?php

/**
 * @group core
 */
class Core_TypedRegistryTest extends TestUtils_TestCase {

	/**
	 * @var \Nano\Util\TypedRegistry
	 */
	protected $registry;

	protected function setUp() {
		$this->registry = new \Nano\Util\TypedRegistry();
	}

	public function testRegisteringProperties() {
		$this->registry->register('foo', 'bar');
		self::assertInternalType('string', $this->registry->foo);
		self::assertEquals('bar', $this->registry->foo);
	}

	public function testSettingPropertiesAsArrayKeys() {
		$this->registry['foo'] = 'bar';
		self::assertEquals('bar', $this->registry->foo);

		$this->registry->baz = 'bar';
		self::assertEquals('bar', $this->registry['baz']);
	}

	public function testAppendShouldThrowException() {
		$this->setExpectedException('RuntimeException', 'Invalid \Nano\\Util\\TypedRegistry usage. Use register() or offsetSet() method.');
		$this->registry->append('foo');
	}

	public function testShouldAcceptAnyValueForNotTypedProperty() {
		$this->registry
			->register('foo', 'bar')
			->register('foo', 100)
			->register('foo', 100.001)
			->register('foo', array())
			->register('foo', new stdClass())
		;
	}

	public function testShouldAcceptValidValueForTypedProperty() {
		$this->registry
			->ensure('foo', 'stdClass')
			->register('foo', new stdClass())
			->register('bar', new stdClass())
			->ensure('bar', 'stdClass')
		;

		self::assertInstanceOf('stdClass', $this->registry->foo);
		self::assertInstanceOf('stdClass', $this->registry->bar);
	}

	public function testEnsureShouldThrowExceptionWhenPropertyAlreadyTyped() {
		$this->setExpectedException('InvalidArgumentException', 'foo is already instance of stdClass');
		$this->registry
			->ensure('foo', 'stdClass')
			->ensure('foo', __CLASS__)
		;
	}

	/**
	 * @dataProvider dataProvider()
	 * @param mixed $actual
	 */
	public function testShouldThrowTypeExceptionWhenInvalidValuePassedForTypedPropertyAfterSetup($actual) {
		$this->setExpectedException('InvalidArgumentException', 'foo should be instance of stdClass');
		$this->registry
			->ensure('foo', 'stdClass')
			->register('foo', $actual)
		;
	}

	/**
	 * @dataProvider dataProvider()
	 * @param mixed $actual
	 */
	public function testShouldThrowTypeExceptionWhenInvalidValuePassedForTypedPropertyBeforeSetup($actual) {
		$this->setExpectedException('InvalidArgumentException', 'foo should be instance of stdClass');
		$this->registry
			->register('foo', $actual)
			->ensure('foo', 'stdClass')
		;
	}

	public function testDetectingIfPropertyReadOnly() {
		self::assertFalse($this->registry->isReadOnly('foo'));
		$this->registry->readOnly('foo', 'bar');
		self::assertTrue($this->registry->isReadOnly('foo'));
		self::assertEquals('bar', $this->registry->foo);
	}

	public function testReadOnlyShouldThrowExceptionWhenDuplicateDefinition() {
		$this->setExpectedException('InvalidArgumentException', 'foo is already read-only property');
		$this->registry->readOnly('foo', 'bar');
		$this->registry->readOnly('foo', 'baz');
	}

	public function testReadOnlyShouldThrowExceptionWhenRegistering() {
		$this->setExpectedException('InvalidArgumentException', 'foo is read-only property');
		$this->registry->readOnly('foo', 'bar');
		$this->registry->register('foo', 'baz');
	}

	public function testReadOnlyShouldThrowExceptionWhenSettingAsArrayKey() {
		$this->setExpectedException('InvalidArgumentException', 'foo is read-only property');
		$this->registry->readOnly('foo', 'bar');
		$this->registry['foo'] = 'baz';
	}

	public function testReadOnlyShouldThrowExceptionWhenSettingAsObjectProperty() {
		$this->setExpectedException('InvalidArgumentException', 'foo is read-only property');
		$this->registry->readOnly('foo', 'bar');
		$this->registry->foo = 'baz';
	}

	public function testMarkReadOnlyWithoutValue() {
		$this->registry->readOnly('foo');
		$this->registry->foo = 'bar';
	}

	public function testChangingReadOnlyAfterDefiningWithoutValue() {
		$this->setExpectedException('InvalidArgumentException', 'foo is read-only property');

		$this->registry->readOnly('foo');
		$this->registry->foo = 'bar';
		$this->registry->foo = 'baz';
	}

	public function dataProvider() {
		return array(
			array(array())
			, array('string')
			, array(100)
			, array(100.001)
			, array($this)
		);
	}

	protected function tearDown() {
		unSet($this->registry);
	}

}