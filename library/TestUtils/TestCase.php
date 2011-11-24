<?php

/**
 * @property TestUtils_Mixin_Connect $connection
 * @property TestUtils_Mixin_Files $files
 */
abstract class TestUtils_TestCase extends PHPUnit_Framework_TestCase {

	/**
	 * @var Application|null
	 */
	private static $backupApplication = null;

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
	 * Asserts the number of elements of an array, Countable or Iterator.
	 *
	 * @param integer $expectedCount
	 * @param mixed   $haystack
	 * @param string  $message
	 */
	public static function assertCount($expectedCount, $haystack, $message = '') {
//		if (!is_int($expectedCount)) {
//			throw PHPUnit_Util_InvalidArgumentHelper::factory(1, 'integer');
//		}
//
//		if (!$haystack instanceof Countable &&
//			!$haystack instanceof Iterator &&
//			!is_array($haystack)) {
//			throw PHPUnit_Util_InvalidArgumentHelper::factory(2, 'countable');
//		}

		self::assertThat($haystack, new TestUtils_Constraint_Count($expectedCount), $message);
	}

	/**
	 * Asserts the number of elements of an array, Countable or Iterator.
	 *
	 * @param integer $expectedCount
	 * @param mixed   $haystack
	 * @param string  $message
	 */
	public static function assertNotCount($expectedCount, $haystack, $message = '') {
		if (!is_int($expectedCount)) {
			throw PHPUnit_Util_InvalidArgumentHelper::factory(1, 'integer');
		}

		if (!$haystack instanceof Countable &&
			!$haystack instanceof Iterator &&
			!is_array($haystack)) {
			throw PHPUnit_Util_InvalidArgumentHelper::factory(2, 'countable');
		}

		$constraint = new PHPUnit_Framework_Constraint_Not(
		  new TestUtils_Constraint_Count($expectedCount)
		);

		self::assertThat($haystack, $constraint, $message);
	}

	protected static function backupCurrentApplication() {
		self::$backupApplication = self::getObjectProperty('Application', 'current');
		self::setObjectProperty('Application', 'current', null);
	}

	protected static function restoreCurrentApplication() {
		if (null !== self::$backupApplication) {
			self::setObjectProperty('Application', 'current', self::$backupApplication);
		}
	}

	/**
	 * @return Nano_C_Response_Test
	 * @param Application $application
	 * @param string $module
	 * @param string $controller
	 * @param string $action
	 * @param array $params
	 */
	protected static function runAction(Application $application, $module, $controller, $action, array $params = array()) {
		/** @var Nano_C $instance */
		$className = Nano_Dispatcher::formatName($controller, true, null === $module ? null : Nano_Modules::nameToNamespace($module));

		$instance  = new $className($application->getDispatcher());
		$instance->setResponse(new \Nano_C_Response_Test());
		$instance->setRenderer(new \Nano_Render($application));

		$params['module']     = $module;
		$params['controller'] = $controller;
		$params['action']     = $action;
		$application->getDispatcher()->setParams($params);

		self::setObjectProperty($application->getDispatcher(), 'controllerInstance', $instance);

		$instance->run($action);
		return $instance->response();
	}

	/**
	 * @return TestUtils_Stub_ReturnReal
	 */
	public function returnReal() {
		return new TestUtils_Stub_ReturnReal();
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

	/**
	 * @return Nano_C_Response_Test
	 * @param string $module
	 * @param string $controller
	 * @param string $action
	 * @param array $params
	 */
	protected function runTestAction($module, $controller, $action, array $params = array()) {
		if (!isSet($this->application)) {
			self::fail('Configure test application');
		}
		return self::runAction($this->application, $module, $controller, $action, $params);
	}

}