<?php

namespace Nano\Route\Section;

/**
 * @property \Nano\Route\Section\Common|null $parent
 */
abstract class Common {

	/**
	 * @var string
	 */
	protected $location;

	/**
	 * @var string
	 */
	protected $module = null;

	/**
	 * @var string
	 */
	protected $suffix = null;

	/**
	 * @var \ArrayObject|\Nano\Route\Section\Common[]
	 */
	protected $sections;

	/**
	 * @var \ArrayObject[]
	 */
	protected $routes;

	/**
	 * @var array
	 */
	protected $params = array();

	/**
	 * @return boolean
	 * @param string $location
	 */
	abstract public function sectionMatches($location);

	/**
	 * @return string
	 * @param string $location
	 */
	abstract public function trimSectionLocation($location);

	/**
	 * @param string $location
	 */
	public function __construct($location) {
		$this->location = $location;
		$this->routes   = new \ArrayObject;
		$this->sections = new \ArrayObject;
	}

	/**
	 * @return \Nano\Route\Section\Common
	 * @param string $location
	 *
	 * @throws \Nano\Exception
	 */
	public static function create($location) {
		if ('' === $location || null === $location) {
			throw new \Nano\Exception('Section location should not be empty');
		}
		if (\Nano\Route\Common::PREFIX_REGEXP == $location[0]) {
			return new \Nano\Route\Section\RegExp(subStr($location, 1));
		}
		return new \Nano\Route\Section\StaticLocation($location);
	}

	/**
	 * @return \Nano\Route\Section\Common
	 * @param string $location
	 *
	 * @throws \Nano\Exception
	 */
	public function section($location) {
		$result = self::create($location);
		$this->sections->append($result);

		$result->setParent($this);
		$result->module($this->module);
		$result->suffix($this->suffix);
		return $result;
	}

	/**
	 * @return \Nano\Route\Section\Common
	 * @param string $location
	 * @param string $controller
	 * @param string $action
	 * @param array $params
	 */
	public function get($location, $controller = 'index', $action = 'index', array $params = array()) {
		return $this->add(__FUNCTION__, $location, $controller, $action, $params);
	}

	/**
	 * @return \Nano\Route\Section\Common
	 * @param string $location
	 * @param string $controller
	 * @param string $action
	 * @param array $params
	 */
	public function post($location, $controller = 'index', $action = 'index', array $params = array()) {
		return $this->add(__FUNCTION__, $location, $controller, $action, $params);
	}

	/**
	 * @return \Nano\Route\Section\Common
	 * @param string $method
	 * @param string $location
	 * @param string $controller
	 * @param string $action
	 * @param array $params
	 */
	public function add($method, $location, $controller = 'index', $action = 'index', array $params = array()) {
		$this->addRoute($method, \Nano\Route\Common::create(
			$this->buildLocation($location)
			, $controller
			, $action
			, $this->module
			, $params
		));
		return $this;
	}

	/**
	 * @return \Nano\Route\Section\Common|null
	 */
	public function end() {
		$result = $this->parent;
		unSet($this->parent);
		return $result;
	}

	/**
	 * @return \Nano\Route\Section\Common
	 * @param string $value
	 */
	public function module($value) {
		$this->module = $value;
		return $this;
	}

	/**
	 * @return \Nano\Route\Section\Common
	 * @param string $value
	 */
	public function suffix($value) {
		$this->suffix = $value;
		return $this;
	}

	/**
	 * @return \Nano\Route\Section\Common
	 * @param string $method
	 * @param \Nano\Route\Common $route
	 */
	public function addRoute($method, \Nano\Route\Common $route) {
		$key = strToLower($method);
		if (!$this->routes->offsetExists($key)) {
			$this->routes->offsetSet($key, new \ArrayObject());
		}
		$this->routes->offsetGet($key)->append($route);
		return $this;
	}

	/**
	 * @return \ArrayObject[]|\ArrayObject
	 */
	public function getRoutes() {
		return $this->routes;
	}

	/**
	 * @return \Nano\Route\Section\Common[]|\ArrayObject
	 */
	public function getSections() {
		return $this->sections;
	}

	/**
	 * @return string
	 */
	public function getLocation() {
		return $this->location;
	}

	/**
	 * @return null|string
	 */
	public function getModule() {
		return $this->module;
	}

	/**
	 * @return null|string
	 */
	public function getSuffix() {
		return $this->suffix;
	}

	/**
	 * @return \Nano\Route\Common|null
	 * @param string $method
	 * @param string $location
	 */
	public function getFor($method, $location) {
		if (!$this->sectionMatches($location)) {
			return null;
		}

		$sectionLocation = $this->trimSectionLocation($location);
		if (($result = $this->findSection($method, $sectionLocation)) instanceof \Nano\Route\Common) {
			return $result;
		}

		return $this->findRoute($method, $sectionLocation);
	}

	public function __sleep() {
		return array('sections', 'routes', 'location');
	}

	/**
	 * @param \Nano\Route\Section\Common $section
	 */
	protected function setParent(\Nano\Route\Section\Common $section) {
		$this->parent = $section;
	}

	/**
	 * @return \Nano\Route\Common|null
	 * @param string $method
	 * @param string $location
	 */
	protected function findSection($method, $location) {
		foreach ($this->sections as $section) {
			$section->params = $this->params;
			if (($route = $section->getFor($method, $location)) instanceof \Nano\Route\Common) {
				return $route;
			}
			$section->params = array();
		}
		return null;
	}

	/**
	 * @return \Nano\Route\Common|null
	 * @param string $method
	 * @param string $location
	 */
	protected function findRoute($method, $location) {
		if (!$this->routes->offsetExists($method)) {
			return null;
		}
		foreach ($this->routes->offsetGet($method) as /** @var \Nano\Route\Common $route */ $route) {
			if ($route->match($location)) {
				$route->addParams($this->params);
				return $route;
			}
		}
		return null;
	}

	/**
	 * @return string
	 * @param string $location
	 */
	protected function buildLocation($location) {
		$isRegExp = false;
		$tests    = array($location, $this->suffix);
		$parts    = array();

		foreach ($tests as $part) {
			if (null === $part || 0 === strLen($part)) {
				continue;
			}
			if (\Nano\Route\Common::PREFIX_REGEXP === $part[0]) {
				$isRegExp = true;
			}
			$parts[] = $part;
		}

		if (false === $isRegExp) {
			return $location . $this->suffix;
		}

		$result = '~';
		foreach ($parts as $part) {
			if (\Nano\Route\Common::PREFIX_REGEXP === (string)$part[0]) {
				$result .= str_replace('/', '\/', subStr($part, 1));
			} else {
				$result .= preg_quote($part, '/');
			}
		}
		return $result;
	}

}