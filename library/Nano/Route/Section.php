<?php

/**
 * @property Nano_Route_Section|null $parent
 */
abstract class Nano_Route_Section {

	/**
	 * @var ArrayObject[]
	 */
	protected $routes;

	/**
	 * @var ArrayObject|Nano_Route_Section[]
	 */
	protected $sections;

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
//		$this->routes   = new SplFixedArray(0);
//		$this->sections = new SplFixedArray(0);
		$this->routes   = new ArrayObject();
		$this->sections = new ArrayObject();
	}

	/**
	 * @return Nano_Route_Section
	 * @param string $location
	 *
	 * @throws Nano_Exception
	 */
	public static function create($location) {
		if ('' === $location || null === $location) {
			throw new Nano_Exception('Section location should not be empty');
		}
		if (Nano_Route_Abstract::PREFIX_REGEXP == $location[0]) {
			return new Nano_Route_Section_RegExp(subStr($location, 1));
		}
		return new Nano_Route_Section_Static($location);
	}

	/**
	 * @return Nano_Route_Section
	 * @param string $location
	 *
	 * @throws Nano_Exception
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
	 * @return Nano_Route_Section
	 * @param string $location
	 * @param string $controller
	 * @param string $action
	 * @param array $params
	 */
	public function get($location, $controller = 'index', $action = 'index', array $params = array()) {
		return $this->add(__FUNCTION__, $location, $controller, $action, $params);
	}

	/**
	 * @return Nano_Route_Section
	 * @param string $location
	 * @param string $controller
	 * @param string $action
	 * @param array $params
	 */
	public function post($location, $controller = 'index', $action = 'index', array $params = array()) {
		return $this->add(__FUNCTION__, $location, $controller, $action, $params);
	}

	/**
	 * @return Nano_Route_Section
	 * @param string $method
	 * @param string $location
	 * @param string $controller
	 * @param string $action
	 * @param array $params
	 */
	public function add($method, $location, $controller = 'index', $action = 'index', array $params = array()) {
		$this->addRoute($method, Nano_Route_Abstract::create(
			$this->buildLocation($location)
			, $controller
			, $action
			, $this->module
			, $params
		));
		return $this;
	}

	/**
	 * @return Nano_Route_Section|null
	 */
	public function end() {
		$result = $this->parent;
		unSet($this->parent);
		return $result;
	}

	/**
	 * @return Nano_Route_Section
	 * @param string $value
	 */
	public function module($value) {
		$this->module = $value;
		return $this;
	}

	/**
	 * @return Nano_Route_Section
	 * @param string $value
	 */
	public function suffix($value) {
		$this->suffix = $value;
		return $this;
	}

	/**
	 * @return Nano_Route_Section
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
	 * @return ArrayObject[]|ArrayObject
	 */
	public function getRoutes() {
		return $this->routes;
	}

	/**
	 * @return Nano_Route_Section[]|ArrayObject
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
	 * @return Nano_Route_Abstract|null
	 * @param string $method
	 * @param string $location
	 */
	public function getFor($method, $location) {
		if (!$this->sectionMatches($location)) {
			return null;
		}

		$sectionLocation = $this->trimSectionLocation($location);
		if (($result = $this->findSection($method, $sectionLocation)) instanceof Nano_Route_Abstract) {
			return $result;
		}

		return $this->findRoute($method, $sectionLocation);
	}

	public function __sleep() {
		return array('sections', 'routes');
	}

	/**
	 * @param Nano_Route_Section $section
	 */
	protected function setParent(Nano_Route_Section $section) {
		$this->parent = $section;
	}

	/**
	 * @return Nano_Route_Abstract|null
	 * @param string $method
	 * @param string $location
	 */
	protected function findSection($method, $location) {
		foreach ($this->sections as $section) {
			if (($route = $section->getFor($method, $location)) instanceof Nano_Route_Abstract) {
				return $route;
			}
		}
		return null;
	}

	/**
	 * @return Nano_Route_Abstract|null
	 * @param string $method
	 * @param string $location
	 */
	protected function findRoute($method, $location) {
		if (!$this->routes->offsetExists($method)) {
			return null;
		}
		foreach ($this->routes->offsetGet($method) as /** @var Nano_Route_Abstract $route */ $route) {
			if ($route->match($location)) {
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
			if (Nano_Route_Abstract::PREFIX_REGEXP === $part[0]) {
				$isRegExp = true;
			}
			$parts[] = $part;
		}

		if (false === $isRegExp) {
			return $location . $this->suffix;
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

}