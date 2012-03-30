<?php

/**
 * @group library
 */
class Library_Validator_RegExpTest extends TestUtils_TestCase {

	public function testIsValidShouldReturnFalseWhenPatternNotMatches() {
		$validator = new Nano_Validator_RegExp('/foo/');
		self::assertFalse($validator->isValid('bar'));
	}

	public function testIsValidShouldReturnTrueWhenPatternMatches() {
		$validator = new Nano_Validator_RegExp('/foo/');
		self::assertTrue($validator->isValid('some foo value'));
	}

}