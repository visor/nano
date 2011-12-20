<?php

/**
 * @group test-utils
 * @group framework
 */
class TestUtils_TestCaseTest extends TestUtils_TestCase {

	protected $protected = 'some protected value';

	private $private = 'some private value';

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