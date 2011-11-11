<?php

/**
 * @group test-utils
 * @group framework
 */
class TestUtils_TestCaseTest extends TestUtils_TestCase {

	protected $protected = 'some protected value';

	private $private = 'some private value';

	public function testAssertExceptionShouldPassWhenExceptionThrows() {
		$closure = function() {
			throw new RuntimeException('Test exception');
		};
		try {
			self::assertException($closure, 'RuntimeException');
		} catch (Exception $e) {
			self::fail('No exception should be here ' . PHP_EOL . $e);
		}
	}

	public function testAssertExceptionShouldPassWithExceptionStringWhenExceptionThrows() {
		$closure = function() {
			throw new RuntimeException('Test exception');
		};
		try {
			self::assertException($closure, 'RuntimeException', 'Test exception');
		} catch (Exception $e) {
			self::fail('No exception should be here ' . PHP_EOL . $e);
		}
	}

	public function testAssertExceptionShouldFailsWhenNoExceptionThrown() {
		$exception = null;
		try {
			self::assertException(function () {}, 'Exception');
		} catch (Exception $e) {
			$exception = $e;
		}

		self::assertInstanceOf('PHPUnit_Framework_AssertionFailedError', $exception);
		self::assertContains('No exception thrown', $exception->getMessage());
		self::assertContains('exception <Exception> should throw', $exception->getMessage());
	}

	public function testAssertExceptionShouldFailsWhenMessageDoesNotMatches() {
		$exception = null;
		$closure   = function() {
			throw new RuntimeException('Test exception message');
		};
		try {
			self::assertException($closure, 'RuntimeException', 'Another exception message');
		} catch (Exception $e) {
			$exception = $e;
		}

		self::assertInstanceOf('PHPUnit_Framework_AssertionFailedError', $exception);
		self::assertContains('Exception message not matches', $exception->getMessage());
		self::assertContains('exception <RuntimeException> with message \'Another exception message\' should throw', $exception->getMessage());
	}

	public function testAssertExceptionShouldFailsWhenExceptionDoesNotMatches() {
		$exception = null;
		$closure   = function() {
			throw new Exception('Test exception');
		};
		try {
			self::assertException($closure, 'RuntimeException');
		} catch (Exception $e) {
			$exception = $e;
		}

		self::assertInstanceOf('PHPUnit_Framework_AssertionFailedError', $exception);
		self::assertContains('Exception class not matches', $exception->getMessage());
		self::assertContains('exception <RuntimeException> should throw', $exception->getMessage());
	}

	public function testAssertNoExceptionShouldPassWhenNoExceptionThrown() {
		$exception = null;
		try {
			self::assertNoException(function() {}, 'RuntimeException');
		} catch (Exception $e) {
			$exception = $e;
		}
		self::assertNull($exception);
	}

	public function testAssertNoExceptionShouldFailsWhenExceptionThrows() {
		$exception = null;
		$closure   = function() {
			throw new Exception('Test exception');
		};
		try {
			self::assertNoException($closure);
		} catch (Exception $e) {
			$exception = $e;
		}

		self::assertInstanceOf('PHPUnit_Framework_AssertionFailedError', $exception);
		self::assertContains('no exception should throw', $exception->getMessage());
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