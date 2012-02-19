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

		$instance  = new $className($application);
		$instance->setResponse(new \Nano_C_Response_Test($application));
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