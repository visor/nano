<?php

/**
 * @group library
 */
class Library_Validator_CompositeTest extends \Nano\TestUtils\TestCase {

	public function testIsValidShouldReturnTrueWhenAllChildValidatorsValid() {
		$validator = new \Nano\Validator\Composite();
		$validator
			->append(new \Nano\Validator\Valid())
			->append(new \Nano\Validator\Valid())
		;
		self::assertTrue($validator->isValid('some value'));
	}

	public function testIsValidShouldReturnTrueWhenAnyChildValidatorInvalid() {
		$validator = new \Nano\Validator\Composite();
		$validator
			->append(new \Nano\Validator\Valid())
			->append(new \Nano\Validator\Valid())
			->append(new \Nano\Validator\Invalid())
		;
		self::assertFalse($validator->isValid('some value'));
	}

	public function testShouldReturnFailedValidatorMessage() {
		$validator = new \Nano\Validator\Composite();
		$validator
			->append(new \Nano\Validator\Valid(), 'first message')
			->append(new \Nano\Validator\Invalid(), 'second message')
		;
		self::assertFalse($validator->isValid('some value'));
		self::assertEquals('second message', $validator->getMessage());
	}

}