<?php

/**
 * @group library
 */
class Library_Form_CommonTest extends TestUtils_TestCase {

	public function testGetValidatorShouldThrowsWhenNotExistedValidatorQueries() {
		$this->setExpectedException('\Nano\Exception', 'Validator for field "foo" not defined');
		$form = new Nano_Form(array('foo'));
		$form->getValidator('foo');
	}

	public function testAddValidatorShouldThrowWhenAlreadyExists() {
		$this->setExpectedException('\Nano\Exception', 'Validator for field "foo" already defined');
		$form = new Nano_Form(array('foo'));
		$form->addValidator('foo', new Nano_Validator_True());
		$form->addValidator('foo', new Nano_Validator_Required());
	}

	public function testAddValidatorShouldSetupMessageWhenPassed() {
		$form    = new Nano_Form(array('foo'));
		$message = 'Validator invalid message';
		$form->addValidator('foo', new Nano_Validator_True(), $message);
		self::assertEquals($message, $form->getValidator('foo')->getMessage());
	}

	public function testPopulateShouldIgnoreUnknownKeys() {
		$form = new Nano_Form(array('foo', 'bar'));
		$form->populate(array(
			'foo' => 'value1'
			, 'bar' => 'value2'
			, 'baz' => 'value3'
		));
		self::assertArrayNotHasKey('ba', $form->getValues());
	}

	public function testPopulate() {
		$form = new Nano_Form(array('f1', 'f2', 'f3'));

		$form->populate(array('f1' => 'v1', 'f2' => 'v2', 'f3' => 'v3'));
		self::assertEquals(3, count($form->getValues()));
		self::assertArrayHasKey('f1', $form->getValues());
		self::assertArrayHasKey('f2', $form->getValues());
		self::assertArrayHasKey('f2', $form->getValues());
		$values = $form->getValues();
		self::assertEquals('v1', $values['f1']);
		self::assertEquals('v2', $values['f2']);
		self::assertEquals('v3', $values['f3']);
	}

	public function testFieldsAccess() {
		$form = new Nano_Form(array('f1', 'f2', 'f3'));
		$form->populate(array('f1' => 'v1'));

		self::assertTrue(isset($form->f1));
		self::assertEquals('v1', $form->f1);
		$form->f1 = 10;
		self::assertEquals('10', $form->f1);
	}

	public function testFormShouldBeValidWhenNoValidators() {
		$form = new Nano_Form(array('f1', 'f2', 'f3'));
		$form->populate(array('f1' => 'v1'));
		self::assertTrue($form->isValid());
	}

	public function testFormShouldBeInvalidWhenAnyValidatorFails() {
		$form = new Nano_Form(array('f1', 'f2', 'f3'));
		$form->addValidator('f1', new Nano_Validator_True());
		$form->populate(array('f1' => 'v1'));
		self::assertTrue($form->isValid());

		$form->addValidator('f2', new Nano_Validator_True());
		self::assertTrue($form->isValid());

		$form->addValidator('f3', new Nano_Validator_False());
		self::assertFalse($form->isValid());
	}

	public function testGetErrorsShouldReturnEmptyArrayWhenFormValid() {
		$form = new Nano_Form(array('foo', 'bar'));
		$form
			->addValidator('foo', new Nano_Validator_Required(), 'foo required')
			->addValidator('bar', new Nano_Validator_Required(), 'bar required')
		;
		$form->populate(array('foo' => 'v1', 'bar' => 'v2'));
		self::assertTrue($form->isValid());
		self::assertCount(0, $form->getErrors());
	}

	public function testGetErrorsShouldReturnInvalidFieldsErrorsWhenFormInvalid() {
		$form = new Nano_Form(array('foo', 'bar'));
		$form
			->addValidator('foo', new Nano_Validator_Required(), 'foo required')
			->addValidator('bar', new Nano_Validator_Required(), 'bar required')
		;
		$form->populate(array('foo' => 'v1'));
		self::assertFalse($form->isValid());
		self::assertCount(1, $form->getErrors());
		self::assertArrayHasKey('bar', $form->getErrors());

		$errors = $form->getErrors();
		self::assertEquals('bar required', $errors['bar']);
	}

	public function testGetFieldErrorShouldReturnNullWhenFieldValid() {
		$form = new Nano_Form(array('foo', 'bar'));
		$form
			->addValidator('foo', new Nano_Validator_Required(), 'foo required')
			->addValidator('bar', new Nano_Validator_Required(), 'bar required')
		;
		$form->populate(array('foo' => 'v1'));
		self::assertFalse($form->isValid());
		self::assertNull($form->getFieldError('foo'));
	}

	public function testGetFieldErrorShouldReturnValidatorMessageWhenFieldInvalid() {
		$form = new Nano_Form(array('foo', 'bar'));
		$form
			->addValidator('foo', new Nano_Validator_Required(), 'foo required')
			->addValidator('bar', new Nano_Validator_Required(), 'bar required')
		;
		$form->populate(array('foo' => 'v1'));
		self::assertFalse($form->isValid());
		self::assertEquals('bar required', $form->getFieldError('bar'));
	}

	public function testValidationShouldStopsOnErrorInStopOnErrorMode() {
		$form = new Nano_Form(array('foo', 'bar'));
		$form
			->setMode(Nano_Form::MODE_STOP_ON_ERROR)
			->addValidator('foo', new Nano_Validator_Required(), 'foo required')
			->addValidator('bar', new Nano_Validator_Required(), 'bar required')
		;

		self::assertFalse($form->isValid());
		self::assertCount(1, $form->getErrors());
		self::assertEquals('foo required', $form->getFieldError('foo'));
		self::assertNull($form->getFieldError('bar'));
	}

	public function testAllValidatorsShouldBeCalledOnValidateAllMode() {
		$form = new Nano_Form(array('foo', 'bar'));
		$form
			->setMode(Nano_Form::MODE_VALIDATE_ALL)
			->addValidator('foo', new Nano_Validator_Required(), 'foo required')
			->addValidator('bar', new Nano_Validator_Required(), 'bar required')
		;

		self::assertFalse($form->isValid());
		self::assertCount(2, $form->getErrors());
		self::assertEquals('foo required', $form->getFieldError('foo'));
		self::assertEquals('bar required', $form->getFieldError('bar'));
	}

	public function testSetValueShouldIgnoreEmpyArrays() {
		$form = new Nano_Form(array('foo'));
		$form->foo = array();
		self::assertNull($form->foo);
	}

	public function testSetValueShouldTrimArrayValues() {
		$form = new Nano_Form(array('foo'));
		$form->foo = array(' some value ');
		self::assertEquals('some value', $form->foo[0]);
	}

	public function testSetValueShouldIgnoreEmptyValuesInArrays() {
		$form = new Nano_Form(array('foo'));
		$form->foo = array('', '   ', array(), array('		'));
		self::assertNull($form->foo);
	}

}