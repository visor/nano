<?php

namespace Nano;

class Dispatcher {

	const SUFFIX_CONTROLLER = 'Controller';
	const SUFFIX_ACTION     = 'Action';

	const ERROR_NOT_FOUND   = 404;
	const ERROR_INTERNAL    = 500;

	const CONTEXT           = 'context';

	/**
	 * @var \Nano\Dispatcher\Custom
	 */
	protected $custom             = null;

	/**
	 * @var string
	 */
	protected $module             = null;

	/**
	 * @var string
	 */
	protected $controller         = null;

	/**
	 * @var \Nano_C
	 */
	protected $controllerInstance = null;

	/**
	 * @var string
	 */
	protected $action             = null;

	/**
	 * @var string[string]
	 */
	protected $params             = array();

	/**
	 * @var boolean
	 */
	protected $throw              = false;

	/**
	 * @var \Nano_C_Response
	 */
	protected $response           = null;

	/**
	 * @return string
	 * @param string $name
	 * @param boolean $controller
	 * @param string|null $module
	 */
	public static function formatName($name, $controller = true, $module = null) {
		$result = \Nano::stringToName($name);
		if ($controller) {
			$result .= self::SUFFIX_CONTROLLER;
			if (null !== $module) {
				$result = $module . NS . $result;
			}
		} else {
			$result  = strToLower($result[0]) . subStr($result, 1);
			$result .= self::SUFFIX_ACTION;
		}

		return $result;
	}

	/**
	 * @return \Nano\Dispatcher
	 * @param \Nano\Dispatcher\Custom $value
	 */
	public function setCustom(\Nano\Dispatcher\Custom $value) {
		$this->custom = $value;
		return $this;
	}

	/**
	 * @return \Nano\Dispatcher
	 * @param boolean $value
	 */
	public function throwExceptions($value) {
		$this->throw = $value;
		return $this;
	}

	/**
	 * @return boolean|null
	 * @param \Nano_Routes $routes
	 * @param string $url
	 *
	 * @throws \Nano_Exception_NotFound
	 */
	public function dispatch(\Nano_Routes $routes, $url) {
		$route = $this->getRoute($routes, $url);
		if (null !== $route) {
			$this->run($route);
			return null;
		}
		if ($this->custom) {
			$result = $this->custom->dispatch();
			if (false === $result) {
				throw new \Nano_Exception_NotFound('Custom dispatcher fails for: ' . $url, $route);
			}
			return $result;
		}

		if (\Nano::app()->errorHandler()) {
			\Nano::app()->errorHandler()->notFound('Route not found for: ' . $url);
		}
		return null;
	}

	/**
	 * @return string
	 * @param \Nano_Route_Abstract $route
	 */
	public function run(\Nano_Route_Abstract $route) {
		if ($route instanceof \Nano_Route_Runnable) {
			/* @var $route \Nano_Route_Runnable */
			$route->run();
			return null;
		}

		$this->buildParams($route->params());
		$this->controllerInstance = $this->getController($route);
		$this->controllerInstance->setResponse($this->getResponse());

		if ($this->param('context')) {
			$this->controllerInstance->context = $this->param('context');
		}

		$this->controllerInstance->run($this->action());
	}

	/**
	 * @return \Nano_C
	 * @param \Nano_Route_Abstract $route
	 *
	 * @throws \Nano_Exception_NotFound
	 * @throws \Nano_Exception_InternalError
	 */
	public function getController(\Nano_Route_Abstract $route) {
		$this->setUpController($route);
		$className = $route->controllerClass();
		if (!class_exists($className)) {
			throw new \Nano_Exception_NotFound('Controller class not found: '. $className, $route);
		}
		$class = new \ReflectionClass($className);
		if (false === $class->isInstantiable() || false === $class->isSubclassOf('Nano_C')) {
			throw new \Nano_Exception_InternalError('Not a controller class: ' . $className);
		}
		return $class->newInstance();
	}

	/**
	 * @return \Nano_Route_Abstract|null
	 * @param \Nano_Routes $routes
	 * @param string $url
	 */
	public function getRoute(\Nano_Routes $routes, $url) {
		$method  = isSet($_SERVER['REQUEST_METHOD']) ? strToLower($_SERVER['REQUEST_METHOD']) : 'get';
		$testUrl = trim($url, '/');
		return $routes->getFor($method, $testUrl);
	}

	/**
	 * @return boolean
	 * @param \Nano_Route_Abstract $route
	 * @param string $url
	 */
	public function test(\Nano_Route_Abstract $route, $url) {
		if (false === $route->match($url)) {
			return false;
		}
		return true;
	}

	/**
	 * @return \Nano_C
	 */
	public function controllerInstance() {
		return $this->controllerInstance;
	}

	public function module() {
		return $this->module;
	}

	/**
	 * @return string
	 */
	public function controller() {
		return $this->controller;
	}

	/**
	 * @return string
	 */
	public function action() {
		return $this->action;
	}

	/**
	 * @return array
	 */
	public function params() {
		return $this->params;
	}

	/**
	 * @return void
	 * @param array $value
	 */
	public function setParams(array $value) {
		$this->buildParams($value);
	}

	/**
	 * @return string
	 * @param string $name
	 * @param mixed $default
	 */
	public function param($name, $default = null) {
		if (array_key_exists($name, $this->params)) {
			return $this->params[$name];
		}
		return $default;
	}

	/**
	 * @return \Nano_C_Response
	 */
	public function getResponse() {
		if (null === $this->response) {
			$this->setResponse(new \Nano_C_Response);
		}
		return $this->response;
	}

	/**
	 * @return \Nano\Dispatcher
	 * @param \Nano_C_Response $value
	 */
	public function setResponse(\Nano_C_Response $value) {
		$this->response = $value;
		return $this;
	}

	/**
	 * @return void
	 * @param array $data
	 */
	protected function buildParams(array $data) {
		$this->params = array();
		foreach ($data as $name => $value) {
			if ('module' === $name) {
				$this->module = $value;
				continue;
			}
			if ('controller' === $name) {
				$this->controller = $value;
				continue;
			}
			if ('action' === $name) {
				$this->action = $value;
				continue;
			}
			$this->params[$name] = $value;
		}
	}

	/**
	 * @return void
	 * @param \Nano_Route_Abstract $route
	 */
	protected function setUpController(\Nano_Route_Abstract $route) {
		$this->action     = $route->action();
		$this->controller = $route->controller();
		$this->module     = $route->module();
	}

}