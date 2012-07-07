<?php

/**
 * @property TestUtils_Mixin_Connect $connection
 * @property TestUtils_Mixin_Files $files
 * @property TestUtils_Mixin_App $app
 */
abstract class TestUtils_TestCase extends PHPUnit_Framework_TestCase {

	public function __construct($name = NULL, array $data = array(), $dataName = '') {
		parent::__construct($name, $data, $dataName);
		$this->addMixin('files', 'TestUtils_Mixin_Files');
		$this->addMixin('connection', 'TestUtils_Mixin_Connect');
		$this->addMixin('app', 'TestUtils_Mixin_App');
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
	 * @return \Nano\Controller\Response\Test
	 * @param \Nano\Application $application
	 * @param string $module
	 * @param string $controller
	 * @param string $action
	 * @param array $params
	 */
	protected static function runAction(\Nano\Application $application, $module, $controller, $action, array $params = array()) {
		/** @var \Nano\Controller $instance */

		$className = null === $module
			? \Nano\Names::applicationClass($controller, \Nano\Names::NAMESPACE_CONTROLLER)
			: \Nano\Names::moduleClass($module, $controller, \Nano\Names::NAMESPACE_CONTROLLER)
		;
		$instance  = new $className($application);
		$instance->setResponse(new \Nano\Controller\Response\Test($application));
		$instance->setRenderer(new \Nano\Render($application));

		$params['module']     = $module;
		$params['controller'] = $controller;
		$params['action']     = $action;
		$application->dispatcher->setParams($params);

		self::setObjectProperty($application->dispatcher, 'controllerInstance', $instance);

		$instance->run($action);
		return $instance->response();
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
	 * @return \Nano\Controller\Response\Test
	 * @param string $module
	 * @param string $controller
	 * @param string $action
	 * @param array $params
	 */
	protected function runTestAction($module, $controller, $action, array $params = array()) {
		if (!isSet($this->application)) {
			throw new RuntimeException('Configure test application');
		}
		return self::runAction($this->application, $module, $controller, $action, $params);
	}

}