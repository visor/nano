<?php

/**
 * @property TestUtils_Mixin_Connect $connection
 * @property TestUtils_Mixin_Files $files
 */
abstract class TestUtils_TestCase extends PHPUnit_Framework_TestCase {

	public function __construct($name = NULL, array $data = array(), $dataName = '') {
		parent::__construct($name, $data, $dataName);
		$this->addMixin('files', 'TestUtils_Mixin_Files');
		$this->addMixin('connection', 'TestUtils_Mixin_Connect');
	}

	public static function getObjectProperty($object, $name) {
		$class    = new ReflectionClass($object);
		$property = $class->getProperty($name);
		if (!$property->isPublic()) {
			$property->setAccessible(true);
		}
		$result = $property->getValue($property->isStatic() ? null : $object);
		if (!$property->isPublic()) {
			$property->setAccessible(false);
		}
		return $result;
	}

	public static function setObjectProperty($object, $name, $value) {
		$class    = new ReflectionClass($object);
		$property = $class->getProperty($name);
		if (!$property->isPublic()) {
			$property->setAccessible(true);
		}
		$result = $property->setValue($property->isStatic() ? null : $object, $value);
		if (!$property->isPublic()) {
			$property->setAccessible(false);
		}
		return $result;
	}

	/**
	 * @return void
	 * @param Closure $runnable
	 * @param string $exceptionClass
	 * @param string|null $exceptionMessage
	 * @param string|null $message
	 */
	public static function assertException(Closure $runnable, $exceptionClass, $exceptionMessage = null, $message = null) {
		$constraint = new TestUtils_Constraint_Exception($exceptionClass, $exceptionMessage);
		self::assertThat($runnable, $constraint, $message);
	}

	/**
	 * @return void
	 * @param Closure $runnable
	 * @param string|null $message
	 */
	public static function assertNoException(Closure $runnable, $message = null) {
		$constraint = new TestUtils_Constraint_NoException();
		self::assertThat($runnable, $constraint, $message);
	}

	/**
	 * @return TestUtils_Stub_ReturnReal
	 */
	public function returnReal() {
		return new TestUtils_Stub_ReturnReal();
	}

	protected function getTestFile($path) {
		$class = new ReflectionClass($this);
		return dirName($class->getFileName()) . '/_files' . $path;
	}

	protected function addMixin($property, $className) {
		if (isset($this->$property)) {
			throw new InvalidArgumentException('$property');
		}

		$class = new ReflectionClass($className);
		if (!$class->isSubclassOf('TestUtils_Mixin')) {
			throw new InvalidArgumentException('$className');
		}
		if (!$class->isInstantiable()) {
			throw new InvalidArgumentException('$className');
		}

		$this->$property = $class->newInstance();
	}

	/**
	 * @return TestUtils_Fixture
	 */
	protected function fixture() {
		return TestUtils_Fixture::instance();
	}

}