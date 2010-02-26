<?php

class Nano_Dispatcher {

	const SUFFIX_CONTROLLER = 'Controller';
	const SUFFIX_ACTION     = 'Action';

	/**
	 * @var Nano_Dispatcher_Custom
	 */
	protected $custom     = null;

	/**
	 * @var string
	 */
	protected $controller = null;

	/**
	 * @var string
	 */
	protected $action     = null;

	/**
	 * @var string[string]
	 */
	protected $params     = array();

	public static function formatName($name, $controller = true) {
		$result = strToLower($name);
		$result = str_replace('-', ' ', $result);
		$result = ucWords($result);
		$result = str_replace(' ', '', $result);
		$result = trim($result);

		if ($controller) {
			$result .= self::SUFFIX_CONTROLLER;
		} else {
			$result  = strToLower($result[0]) . subStr($result, 1);
			$result .= self::SUFFIX_ACTION;
		}

		return $result;
	}

	/**
	 * @return Nano_Dispatcher
	 */
	public function clean() {
		$this->controller = null;
		$this->action     = null;
		$this->params     = array();
		return $this;
	}

	/**
	 * @return Dispatcher
	 * @param Nano_Dispatcher_Custom $value
	 */
	public function setCustom(Nano_Dispatcher_Custom $value) {
		$this->custom = $value;
		return $this;
	}

	/**
	 * @return string
	 * @param Routes $routes
	 * @param string $url
	 */
	public function dispatch(Nano_Routes $routes, $url) {
		$route = $this->getRoute($routes, $url);
		if (null !== $route) {
			return $this->run($route);
		}
		if ($this->custom) {
			$result = $this->custom->dispatch();
			if (false !== $result) {
				return $result;
			}
			throw new Exception('404');
		}
		throw new Exception('404');
	}

	/**
	 * @return string
	 * @param Nano_Route $route
	 */
	public function run(Nano_Route $route) {
		return $this->getController($route)->run($this->action());
	}

	/**
	 * @return Nano_C
	 * @param Nano_Route $route
	 */
	public function getController(Nano_Route $route) {
		$this->setUpController($route);
		$className = self::formatName($this->controller(), true);
		if (!class_exists($className)) {
			throw new Exception('404');
		}
		$class = new ReflectionClass($className);
		if (false === $class->isInstantiable() || false === $class->isSubclassOf('Nano_C')) {
			throw new Exception('500');
		}
		return $class->newInstance($this);
	}

	/**
	 * @return Nano_Route|null
	 * @param Nano_Routes $routes
	 * @param string $url
	 */
	public function getRoute(Nano_Routes $routes, $url) {
		$testUrl = trim($url, '/');
		foreach ($routes as $route) { /** @var $route Route */
			if ($this->test($route, $testUrl)) {
				return $route;
			}
		}
		return null;
	}

	/**
	 * @return boolean
	 * @param Nano_Route $route
	 * @param string $url
	 */
	public function test(Nano_Route $route, $url) {
		$matches = array();
		$result  = (1 == preg_match($route->pattern(), $url, $matches));
		if (false === $result) {
			return false;
		}

		$this->buildParams($matches);
		return true;
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
	 * @return void
	 * @param array $data
	 */
	protected function buildParams($data) {
		$this->params = array();
		foreach ($data as $name => $value) {
			if ('controller' === $name) {
				$this->controller = $value;
				continue;
			}
			if ('action' === $name) {
				$this->action = $value;
				continue;
			}
			if (is_string($name)) {
				$this->params[$name] = $value;
			}
		}
	}

	/**
	 * @return void
	 * @param Nano_Route $route
	 */
	protected function setUpController(Nano_Route $route) {
		if (null === $this->action) {
			$this->action = $route->action();
		}
		if (null === $this->controller) {
			$this->controller = $route->controller();
		}
	}

}
