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

	public static function assertException($runnable, $class, $message) {
		try {
			$runnable();
			self::fail('No exception thrown');
		} catch (Exception $e) {
			if ($e instanceof PHPUnit_Framework_AssertionFailedError && $e->getMessage() === 'No exception thrown') {
				throw $e;
			}
			if ($e instanceof $class) {
				if ($message) {
					$messageConstraint = new PHPUnit_Framework_Constraint_StringContains($message, true);
					$messageConstraint->evaluate($e->getMessage(), $e->getMessage(), PHP_EOL . $e->getTraceAsString());
				}
				return;
			}

			$constraint = new PHPUnit_Framework_Constraint_IsInstanceOf($class);
			$constraint->evaluate(get_class($e), 'Expected ' . $class . ' but ' . get_class($e) . ' with message "' . $e->getMessage() . '"' . PHP_EOL . $e->getTraceAsString());
		}
	}

	public static function assertNoException($runnable) {
		try {
			$runnable();
		} catch (Exception $e) {
			self::fail(
				'Should not throw any exception but ' . get_class($e) . ' with message <' . $e->getMessage() . '>'
				. PHP_EOL . $e->getTraceAsString()
			);
		}
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