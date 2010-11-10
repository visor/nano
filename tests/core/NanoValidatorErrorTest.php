<?php

/**
 * @group framework
 */
class NanoValidatorErrorTest extends TestUtils_TestCase {

	protected function setUp() {
		require_once $this->files->get($this, DS . 'TestForNanoValidatorError.php');
	}

	public function testErrorMessages() {
		self::assertEquals(TestForNanoValidatorError::ERROR1, $this->createError('ERROR1')->getMessage());
		self::assertEquals(TestForNanoValidatorError::ERROR2, $this->createError('ERROR2')->getMessage());
		self::assertEquals(Nano_Validator_Error::UNKNOWN_ERROR, $this->createError('SOME')->getMessage());
		self::assertEquals(Nano_Validator_Error::UNKNOWN_ERROR, $this->createError('ERROR3')->getMessage());
	}

	/**
	 * @param string $code
	 * @return TestForNanoValidatorError
	 */
	protected function createError($code) {
		return new TestForNanoValidatorError($code);
	}

}