<?php

namespace Nano\Route;

abstract class Common {

	const PREFIX_REGEXP = '~';

	protected $location       = null;
	protected $module         = null;
	protected $controller     = null;
	protected $action         = null;
	protected $params         = null;
	protected $matches        = null;
	protected $compiledParams = null;

	public function __construct($location, $controller = 'index', $action = 'index', $module = null, array $params = array()) {
		$this->location   = $location;
		$this->module     = $module;
		$this->controller = $controller;
		$this->action     = $action;
		$this->params     = $params;
	}

	/**
	 * @return boolean
	 * @param string $location
	 */
	abstract public function match($location);

	/**
	 * @return \Nano\Route\Common
	 * @param string $location
	 * @param string $controller
	 * @param string $action
	 * @param string $module
	 * @param array $params
	 */
	public static function create($location, $controller = 'index', $action = 'index', $module = null, array $params = array()) {
		if ('' === $location || null === $location) {
			return new \Nano\Route\StaticLocation($location, $controller, $action, $module, $params);
		}
		if (self::PREFIX_REGEXP == $location[0]) {
			return new \Nano\Route\RegExp(subStr($location, 1), $controller, $action, $module, $params);
		}
		return new \Nano\Route\StaticLocation($location, $controller, $action, $module, $params);
	}

	/**
	 * @return string
	 */
	public function location() {
		return $this->location;
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
	public function controllerClass() {
		if (null === $this->module) {
			return \Nano\Names::applicationClass($this->controller, \Nano\Names::NAMESPACE_CONTROLLER);
		}
		return \Nano\Names::moduleClass($this->module, $this->controller, \Nano\Names::NAMESPACE_CONTROLLER);
	}

	/**
	 * @return string
	 */
	public function action() {
		return $this->action;
	}

	/**
	 * @return string[]
	 */
	public function matches() {
		return $this->matches;
	}

	/**
	 * @return array
	 */
	public function params() {
		if (null === $this->compiledParams) {
			$this->compiledParams = $this->params;
			if (is_array($this->matches())) {
				foreach ($this->matches() as $name => $value) {
					if (is_string($name)) {
						$this->compiledParams[$name] = $value;
					}
				}
			}
		}
		return $this->compiledParams;
	}

	public function addParams(array $params) {
		$this->compiledParams = null;
		foreach ($params as $name => $value) {
			$this->params[$name] = $value;
		}
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->controller() . '::' . $this->action() . '() when location matches [' . $this->location() . ']';
	}

	public function __sleep() {
		return array('location', 'module', 'controller', 'action', 'params');
	}

}