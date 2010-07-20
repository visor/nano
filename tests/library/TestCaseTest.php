<?php

class TestCaseTest extends TestUtils_TestCase {

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

}