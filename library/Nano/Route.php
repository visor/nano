<?php

abstract class Nano_Route {

	const PREFIX_REGEXP = '~';

	protected $location    = null;
	protected $module      = null;
	protected $controller  = null;
	protected $action      = null;
	protected $matches     = null;
	protected $application = null;

	public function __construct($location, $controller = 'index', $action = 'index', $module = null) {
		$this->location   = $location;
		$this->module     = $module;
		$this->controller = $controller;
		$this->action     = $action;
	}

	/**
	 * @return boolean
	 * @param string $location
	 */
	abstract public function match($location);

	/**
	 * @return Nano_Route
	 * @param string $location
	 * @param string $controller
	 * @param string $action
	 * @param string $module
	 */
	public static function create($location, $controller = 'index', $action = 'index', $module = null) {
		if ('' === $location || null === $location) {
			return new Nano_Route_Static($location, $controller, $action, $module);
		}
		if (self::PREFIX_REGEXP == $location[0]) {
			return new Nano_Route_RegExp(subStr($location, 1), $controller, $action, $module);
		}
		return new Nano_Route_Static($location, $controller, $action, $module);
	}

	public function setApplication(Application $value) {
		$this->application = $value;
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
			return Nano_Dispatcher::formatName($this->controller, true);
		}
		return Nano_Dispatcher::formatName($this->controller, true, Nano_Modules::nameToNamespace($this->module));
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
	 * @return string
	 */
	public function __toString() {
		return $this->controller() . '::' . $this->action() . '() when location matches [' . $this->location() . ']';
	}

	public function __sleep() {
		return array('location', 'module', 'controller', 'action');
	}

}