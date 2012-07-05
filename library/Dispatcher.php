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
	 * @param \Nano\Routes $routes
	 * @param string $url
	 *
	 * @throws \Nano\Exception\NotFound
	 */
	public function dispatch(\Nano\Routes $routes, $url) {
		$route = $this->getRoute($routes, $url);
		if (null !== $route) {
			$this->run($route);
			return null;
		}
		if ($this->custom) {
			$result = $this->custom->dispatch();
			if (false === $result) {
				throw new \Nano\Exception\NotFound('Custom dispatcher fails for: ' . $url, $route);
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
	 * @param \Nano\Route\Common $route
	 */
	public function run(\Nano\Route\Common $route) {
		if ($route instanceof \Nano\Route\Runnable) {
			/* @var $route \Nano\Route\Runnable */
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
	 * @param \Nano\Route\Common $route
	 *
	 * @throws \Nano\Exception\NotFound
	 * @throws \Nano\Exception\InternalError
	 */
	public function getController(\Nano\Route\Common $route) {
		$this->setUpController($route);
		$className = $route->controllerClass();
		if (!class_exists($className)) {
			throw new \Nano\Exception\NotFound('Controller class not found: '. $className, $route);
		}
		$class = new \ReflectionClass($className);
		if (false === $class->isInstantiable() || false === $class->isSubclassOf('Nano_C')) {
			throw new \Nano\Exception\InternalError('Not a controller class: ' . $className);
		}
		return $class->newInstance();
	}

	/**
	 * @return \Nano\Route\Common|null
	 * @param \Nano\Routes $routes
	 * @param string $url
	 */
	public function getRoute(\Nano\Routes $routes, $url) {
		$method  = isSet($_SERVER['REQUEST_METHOD']) ? strToLower($_SERVER['REQUEST_METHOD']) : 'get';
		$testUrl = trim($url, '/');
		return $routes->getFor($method, $testUrl);
	}

	/**
	 * @return boolean
	 * @param \Nano\Route\Common $route
	 * @param string $url
	 */
	public function test(\Nano\Route\Common $route, $url) {
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
	 * @param \Nano\Route\Common $route
	 */
	protected function setUpController(\Nano\Route\Common $route) {
		$this->action     = $route->action();
		$this->controller = $route->controller();
		$this->module     = $route->module();
	}

}