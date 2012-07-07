<?php

/**
 * @group library
 */
class Library_Validator_ValuesTest extends \Nano\TestUtils\TestCase {

	public function testIsValidShouldReturnFaseWhenTestValueNotInOptions() {
		$validator = new \Nano\Validator\Values(array('key1' => 'title1', 'key2' => 'title2'));
		self::assertFalse($validator->isValid('some other key'));
	}

	public function testIsValidShouldReturnTrueWhenTestValueInOptions() {
		$validator = new \Nano\Validator\Values(array('key1' => 'title1', 'key2' => 'title2'));
		self::assertTrue($validator->isValid('key1'));
	}

}