<?php

/**
 * @group library
 */
class Library_Validator_RegExpTest extends \Nano\TestUtils\TestCase {

	public function testIsValidShouldReturnFalseWhenPatternNotMatches() {
		$validator = new \Nano\Validator\RegExp('/foo/');
		self::assertFalse($validator->isValid('bar'));
	}

	public function testIsValidShouldReturnTrueWhenPatternMatches() {
		$validator = new \Nano\Validator\RegExp('/foo/');
		self::assertTrue($validator->isValid('some foo value'));
	}

}