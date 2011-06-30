<?php

class Nano_Routes implements IteratorAggregate {

	/**
	 * @var ArrayObject
	 */
	protected $routes;

	/**
	 * @var string
	 */
	protected $prefix = null;

	/**
	 * @var string
	 */
	protected $suffix = null;

	/**
	 * @var string
	 */
	protected $module = null;

	public function __construct() {
		$this->routes = new ArrayObject();
	}

	/**
	 * @return Nano_Routes
	 * @param string $location
	 */
	public function prefix($location) {
		$this->prefix = $location;
		return $this;
	}

	/**
	 * @return Nano_Routes
	 * @param string $location
	 */
	public function suffix($location) {
		$this->suffix = $location;
		return $this;
	}

	/**
	 * @return Nano_Routes
	 * @param string $name
	 */
	public function module($name) {
		$this->module = $name;
		return $this;
	}

	/**
	 * @return Nano_Routes
	 * @param string $location
	 * @param string $controller
	 * @param string $action
	 */
	public function get($location, $controller = 'index', $action = 'index') {
		return self::add(__FUNCTION__, $location, $controller, $action);
	}

	/**
	 * @return Nano_Routes
	 * @param string $location
	 * @param string $controller
	 * @param string $action
	 */
	public function post($location, $controller = 'index', $action = 'index') {
		return self::add(__FUNCTION__, $location, $controller, $action);
	}

	/**
	 * @return Nano_Routes
	 * @param string $location
	 * @param string $controller
	 * @param string $action
	 */
	public function head($location, $controller = 'index', $action = 'index') {
		return self::add(__FUNCTION__, $location, $controller, $action);
	}

	/**
	 * @return Nano_Routes
	 * @param string $method
	 * @param string $location
	 * @param string $controller
	 * @param string $action
	 */
	public function add($method, $location, $controller = 'index', $action = 'index') {
		$this->addRoute(
			$method
			, Nano_Route::create(
				(string)$this->prefix . $location . (string)$this->suffix
				, $controller
				, $action
				, $this->module
			)
		);
		return $this;
	}

	/**
	 * @return Nano_Routes
	 * @param string $method
	 * @param Nano_Route $route
	 */
	public function addRoute($method, Nano_Route $route) {
		$key = strToLower($method);
		if (!$this->routes->offsetExists($key)) {
			$this->routes->offsetSet($key, new ArrayObject());
		}
		$this->routes->offsetGet($key)->offsetSet($route->location(), $route);
		return $this;
	}

	/**
	 * @return ArrayIterator
	 * @param string $method
	 */
	public function getRoutes($method) {
		$key = strToLower($method);
		if (!$this->routes->offsetExists($key)) {
			return array();
		}
		return $this->routes->offsetGet($key);
	}

	/**
	 * @return ArrayIterator
	 */
	public function getIterator() {
		return new RecursiveArrayIterator($this->routes->getIterator());
	}

	/**
	 * @return string
	 */
	public function __toString() {
		$result = '';
		foreach ($this->routes as $method => $routes) {/** @var ArrayObject $routes */
			$result .= $method . PHP_EOL;
			foreach ($routes->getArrayCopy() as $location => $route) {/** @var Nano_Route $route */
				$result .= '	' . $route->__toString() . PHP_EOL;
			}
		}

		return $result;
	}

}