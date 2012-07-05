<?php

/**
 * @group library
 */
class Library_Exception_DescribeTest extends TestUtils_TestCase {

	/**
	 * @var Library_Exception_TestException
	 */
	protected $exception;

	protected function setUp() {
		include_once __DIR__ . '/_files/TestException.php';
		$this->exception = new Library_Exception_TestException();
	}

	public function testDescribeShouldReturnNullStringForNullValue() {
		self::assertEquals(\Nano\Exception::VALUE_NULL, $this->exception->describe(null));
	}

	public function testDescribeShouldReturnClassNameForObjects() {
		self::assertEquals(__CLASS__, $this->exception->describe($this));
		self::assertEquals('stdClass', $this->exception->describe(new stdClass()));
	}

	public function testDescribeShouldReturnVarExportForArrays() {
		self::assertEquals('array (' . PHP_EOL . ')', $this->exception->describe(array()));
	}

	public function testDescribeShouldReturnVarExportForScalars() {
		self::assertEquals('100', $this->exception->describe(100));
		self::assertEquals('\'100\'', $this->exception->describe('100'));
	}

	protected function tearDown() {
		unSet($this->exception);
	}

}