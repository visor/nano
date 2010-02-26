<?php

/**
 * @property TestUtils_Mixin_Files $files
 */
class TestUtils_TestCase extends PHPUnit_Framework_TestCase {

	public function __construct($name = NULL, array $data = array(), $dataName = '') {
		parent::__construct($name, $data, $dataName);
		$this->addMixin('files', 'TestUtils_Mixin_Files');
	}

	public static function assertException($runnable, $class, $message) {
		try {
			$runnable();
			self::fail('No exception thrown');
		} catch (Exception $e) {
			if ($e instanceof $class) {
				$messageConstraint = new PHPUnit_Framework_Constraint_StringContains($message, true);
				if (!$messageConstraint->evaluate($e->getMessage())) {
					throw $messageConstraint->fail($e->getMessage(), PHP_EOL . $e->getTraceAsString());
				}
				self::assertTrue(true); // update assertion counter
				self::assertTrue(true); // update assertion counter
			} else {
				if ($e instanceof PHPUnit_Framework_AssertionFailedError && $e->getMessage() === 'No exception thrown') {
					throw $e;
				}
				$constraint = new PHPUnit_Framework_Constraint_IsInstanceOf($class);
				throw $constraint->fail($e, 'Expected ' . $class . ' but ' . get_class($e) . ' with message "' . $e->getMessage() . '"' . PHP_EOL . $e->getTraceAsString());
			}
		}
	}

	public static function assertNoException($runnable) {
		try {
			$runnable();
		} catch (Exception $e) {
			self::fail('Should not throw any exception but ' . get_class($e) . ' with message <' . $e->getMessage() . '>');
		}
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


}