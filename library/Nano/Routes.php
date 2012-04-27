<?php

class Nano_Routes implements IteratorAggregate {

	/**
	 * @var ArrayObject
	 */
	private static $empty = null;

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
	 * @param array $params
	 */
	public function get($location, $controller = 'index', $action = 'index', array $params = array()) {
		return $this->add(__FUNCTION__, $location, $controller, $action, $params);
	}

	/**
	 * @return Nano_Routes
	 * @param string $location
	 * @param string $controller
	 * @param string $action
	 * @param array $params
	 */
	public function post($location, $controller = 'index', $action = 'index', array $params = array()) {
		return $this->add(__FUNCTION__, $location, $controller, $action, $params);
	}

	/**
	 * @return Nano_Routes
	 * @param string $method
	 * @param string $location
	 * @param string $controller
	 * @param string $action
	 * @param array $params
	 */
	public function add($method, $location, $controller = 'index', $action = 'index', array $params = array()) {
		$this->addRoute($method, Nano_Route_Abstract::create(
			$this->getLocation($location)
			, $controller
			, $action
			, $this->module
			, $params
		));
		return $this;
	}

	/**
	 * @return Nano_Routes
	 * @param string $method
	 * @param Nano_Route_Abstract $route
	 */
	public function addRoute($method, Nano_Route_Abstract $route) {
		$key = strToLower($method);
		if (!$this->routes->offsetExists($key)) {
			$this->routes->offsetSet($key, new ArrayObject());
		}
		$this->routes->offsetGet($key)->append($route);
		return $this;
	}

	/**
	 * @return ArrayObject
	 * @param string $method
	 */
	public function getRoutes($method) {
		$key = strToLower($method);
		if (!$this->routes->offsetExists($key)) {
			return $this->getEmpty();
		}
		return $this->routes->offsetGet($key);
	}

	/**
	 * @return ArrayIterator
	 */
	public function getIterator() {
		return $this->routes->getIterator();
	}

	/**
	 * @return string
	 * @param string $location
	 */
	protected function getLocation($location) {
		$isRegExp = false;
		$tests    = array($this->prefix, $location, $this->suffix);
		$parts    = array();
		foreach ($tests as $part) {
			if (null === $part || 0 === strLen($part)) {
				continue;
			}
			if (Nano_Route_Abstract::PREFIX_REGEXP === $part[0]) {
				$isRegExp = true;
			}
			$parts[] = $part;
		}

		if (false === $isRegExp) {
			return $this->prefix . $location . $this->suffix;
		}
		$result = '~';
		foreach ($parts as $part) {
			if (Nano_Route_Abstract::PREFIX_REGEXP === (string)$part[0]) {
				$result .= str_replace('/', '\/', subStr($part, 1));
			} else {
				$result .= preg_quote($part, '/');
			}
		}
		return $result;
	}

	/**
	 * @return ArrayObject
	 */
	protected function getEmpty() {
		if (null === self::$empty) {
			self::$empty = new ArrayObject();
		}
		return self::$empty;
	}

	public function __sleep() {
		return array('routes');
	}

}