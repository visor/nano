<?php

/**
 * @group test-utils
 * @group library
 */
class TestUtils_TestCaseTest extends \Nano\TestUtils\TestCase {

	protected $protected = 'some protected value';

	private $private = 'some private value';

	public function testShouldCreateMixinsInConstructor() {
		$testCase = new self('test');
		self::assertInstanceOf('\Nano\TestUtils\Mixin\Files', $testCase->files);
		self::assertInstanceOf('\Nano\TestUtils\Mixin\Connect', $testCase->connection);
	}

	public function testAddMixinShouldThrowExceptionWhenPropertyExists() {
		$this->setExpectedException('InvalidArgumentException', '$property');
		$this->addMixin('files', 'stdClass');
	}

	public function testAddMixinShouldThrowExceptionWhenNotMixinClassPassed() {
		$this->setExpectedException('InvalidArgumentException', '$className');
		$this->addMixin('shouldNotSet', 'stdClass');
	}

	public function testAddMixinShouldThrowExceptionWhenAbstractClassPassed() {
		$this->setExpectedException('InvalidArgumentException', '$className');
		$this->addMixin('shouldNotSet', 'Abstract_Test_Mixin');
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

	public function testRunTestActionShouldThrowRuntimeExceptionWhenApplicationPropertyNotExists() {
		$this->setExpectedException('RuntimeException', 'Configure test application');
		unSet($this->application);
		$this->runTestAction('module', 'controller', 'action');
	}

	public function testRunTestActionShouldReturnActionResponse() {
		$this->application = $GLOBALS['application'];
		$result = $this->runTestAction(null, 'response-test', 'set-body');
		self::assertInstanceOf('\Nano\Controller\Response\Test', $result);
	}

	protected function tearDown() {
		unSet($this->application);
	}

}

abstract class Abstract_Test_Mixin extends \Nano\TestUtils\Mixin {}