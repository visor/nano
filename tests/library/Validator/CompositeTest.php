<?php

/**
 * @group library
 */
class Library_Validator_CompositeTest extends TestUtils_TestCase {

	public function testIsValidShouldReturnTrueWhenAllChildValidatorsValid() {
		$validator = new Nano_Validator_Composite();
		$validator
			->append(new Nano_Validator_True())
			->append(new Nano_Validator_True())
		;
		self::assertTrue($validator->isValid('some value'));
	}

	public function testIsValidShouldReturnTrueWhenAnyChildValidatorInvalid() {
		$validator = new Nano_Validator_Composite();
		$validator
			->append(new Nano_Validator_True())
			->append(new Nano_Validator_True())
			->append(new Nano_Validator_False())
		;
		self::assertFalse($validator->isValid('some value'));
	}

	public function testShouldReturnFailedValidatorMessage() {
		$validator = new Nano_Validator_Composite();
		$validator
			->append(new Nano_Validator_True(), 'first message')
			->append(new Nano_Validator_False(), 'second message')
		;
		self::assertFalse($validator->isValid('some value'));
		self::assertEquals('second message', $validator->getMessage());
	}

}