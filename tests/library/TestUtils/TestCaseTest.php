<?php

/**
 * @group test-utils
 */
class TestUtils_TestCaseTest extends TestUtils_TestCase {

	protected $protected = 'some protected value';

	private $private = 'some private value';

	public function testAssertExceptionNoException() {
		$this->setExpectedException('PHPUnit_Framework_AssertionFailedError', 'No exception thrown');
		self::assertException(function () {}, 'Exception', '');
	}

	public function testAssertExceptionWrongExceptionClass() {
		$this->setExpectedException('PHPUnit_Framework_AssertionFailedError', 'Failed asserting that <Exception> is an instance of class "RuntimeException"');
		self::assertException(function () { throw new Exception(); }, 'RuntimeException', '');
	}

	public function testAssertExceptionWrongMessage() {
		$this->setExpectedException('PHPUnit_Framework_AssertionFailedError', 'Failed asserting that <string:foo> contains "bar"');
		self::assertException(function () { throw new Exception('foo'); }, 'Exception', 'bar');
	}

	public function testNonPublicPropertyGet() {
		self::assertEquals($this->protected, self::getObjectProperty($this, 'protected'));
		self::assertEquals($this->private, self::getObjectProperty($this, 'private'));
	}

	public function testNonPublicPropertySet() {
		self::setObjectProperty($this, 'protected', array(1 => 2));
		self::assertEquals(array(1 => 2), $this->protected);

		self::setObjectProperty($this, 'private', null);
		self::assertNull($this->private);
	}

}