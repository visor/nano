<?php

/**
 * @group framework
 * @group orm
 */
class Library_Orm_Model_Test extends TestUtils_TestCase {

	protected function setUp() {
		include_once $this->files->get($this, '/TestDataSource.php');
		include_once $this->files->get($this, '/mapper/Address.php');
		include_once $this->files->get($this, '/mapper/House.php');
		include_once $this->files->get($this, '/mapper/Wizard.php');
		include_once $this->files->get($this, '/mapper/Student.php');
		include_once $this->files->get($this, '/model/Address.php');
		include_once $this->files->get($this, '/model/House.php');
		include_once $this->files->get($this, '/model/Wizard.php');
		include_once $this->files->get($this, '/model/Student.php');

		Orm::instance()->addSource('test', new Library_Orm_TestDataSource(array()));
	}

	public function testGettingMapper() {
		$address = new Library_Orm_Example_Address();
		$house   = new LibraryOrmExampleHouse();
		$wizard  = new Library_OrmExampleWizard();

		self::assertInstanceOf('Mapper_Library_Orm_Example_Address', Library_Orm_Example_Address::mapper());
		self::assertInstanceOf('Mapper_LibraryOrmExampleHouse', LibraryOrmExampleHouse::mapper());
		self::assertInstanceOf('Mapper_Library_OrmExampleWizard', Library_OrmExampleWizard::mapper());
	}

	public function testMapperShouldCreatedOnceForOneModelInstances() {
		self::assertSame(Orm::mapper('Library_Orm_Example_Address'), Library_Orm_Example_Address::mapper());
	}

	public function testGettingUnknownFieldShouldThrowException() {
		self::setExpectedException('Orm_Exception_UnknownField', 'Unknown resource field: address.field');
		$address = new Library_Orm_Example_Address();
		$address->field;
	}

	public function testCreatingEmptyModelInstance() {
		$address = new Library_Orm_Example_Address();
		self::assertTrue(isSet($address->id));
		self::assertTrue(isSet($address->location));
		self::assertNull($address->id);
		self::assertNull($address->location);
	}

	public function testSettingModelField() {
		$value   = 'Number 4, Privet Drive';
		$address = new Library_Orm_Example_Address();
		$address->location = $value;

		$data     = self::getObjectProperty($address, 'data');
		$original = self::getObjectProperty($address, 'original');
		$changed  = self::getObjectProperty($address, 'changedFields');

		self::assertNotNull($data->location);
		self::assertEquals($value, $data->location);
		self::assertObjectNotHasAttribute('location', $original);
		self::assertArrayHasKey('location', $changed);
		self::assertTrue($address->changed());
	}

	public function testSettingSameValueshouldNotUpdateAnyInternalData() {
		$value   = 'Number 4, Privet Drive';
		$address = new Library_Orm_Example_Address(array('location' => $value));
		$address->location = $value;

		$data     = self::getObjectProperty($address, 'data');
		$original = self::getObjectProperty($address, 'original');
		$changed  = self::getObjectProperty($address, 'changedFields');

		self::assertEquals($value, $data->location);
		self::assertObjectNotHasAttribute('location', $original);
		self::assertArrayNotHasKey('location', $changed);
		self::assertFalse($address->changed());
	}

	public function testSettingAnotherValueShouldSaveOriginalValueFirstTime() {
		$value   = 'Number 4, Privet Drive';
		$another = $value . ', Little Whinging';
		$address = new Library_Orm_Example_Address(array('location' => $value));
		$address->location = $another;

		$data     = self::getObjectProperty($address, 'data');
		$original = self::getObjectProperty($address, 'original');
		$changed  = self::getObjectProperty($address, 'changedFields');

		self::assertEquals($another, $data->location);
		self::assertObjectHasAttribute('location', $original);
		self::assertEquals($value, $original->location);
		self::assertArrayHasKey('location', $changed);
		self::assertTrue($address->changed());

		$yetAnother = $another . ', Surrey';
		$address->location = $yetAnother;

		$data     = self::getObjectProperty($address, 'data');
		$original = self::getObjectProperty($address, 'original');
		$changed  = self::getObjectProperty($address, 'changedFields');

		self::assertEquals($yetAnother, $data->location);
		self::assertObjectHasAttribute('location', $original);
		self::assertEquals($value, $original->location);
		self::assertArrayHasKey('location', $changed);
		self::assertTrue($address->changed());

		return $address;
	}

	public function testSettingToOriginalValueShouldMarkFieldAsNotChanged() {
		$value   = 'Number 4, Privet Drive';
		$address = $this->testSettingAnotherValueShouldSaveOriginalValueFirstTime();
		$address->location = $value;

		$data     = self::getObjectProperty($address, 'data');
		$original = self::getObjectProperty($address, 'original');
		$changed  = self::getObjectProperty($address, 'changedFields');

		self::assertEquals($value, $data->location);
		self::assertObjectNotHasAttribute('location', $original);
		self::assertArrayNotHasKey('location', $changed);
		self::assertFalse($address->changed());
	}

	public function testSettingReadOnlyFieldsShouldThrowException() {
		self::setExpectedException('Orm_Exception_ReadonlyField', 'Field address.id is read only');
		$address = new Library_Orm_Example_Address();
		$address->id = 'value';
	}

	public function testSettingUnknownFieldsShouldThrowException() {
		self::setExpectedException('Orm_Exception_UnknownField', 'Unknown resource field: address.field');
		$address = new Library_Orm_Example_Address();
		$address->field = 'value';
	}

	public function testDetectingModelIsNew() {
		$address = new Library_Orm_Example_Student();
		self::assertTrue($address->isNew());

		$address->wizardId = 100;
		self::assertTrue($address->isNew());

		$address = new Library_Orm_Example_Student(array('wizardId' => 100));
		self::assertTrue($address->isNew());

		self::assertFalse(Library_Orm_Example_Address::mapper()->get(1)->isNew());
	}

}