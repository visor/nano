<?php

class Nano_Route {

	protected $pattern    = null;
	protected $controller = null;
	protected $action     = null;
	protected $matches    = null;

	public function __construct($pattern, $controller = 'index', $action = 'index') {
		if (null !== $pattern) {
			$this->pattern = '/^' . str_replace('/','\/', $pattern) . '$/';
		}
		$this->controller = $controller;
		$this->action     = $action;
	}

	/**
	 * @return Nano_Route
	 * @param string $pattern
	 * @param string $controller
	 * @param string $action
	 */
	public static function create($pattern, $controller = 'index', $action = 'index') {
		return new self($pattern, $controller, $action);
	}

	/**
	 * @return string
	 */
	public function pattern() {
		return $this->pattern;
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
	 * @return boolean
	 * @param string $url
	 */
	public function match($url) {
		$this->matches = array();
		if (null === $this->pattern) {
			return true;
		}
		return (1 == preg_match($this->pattern, $url, $this->matches));
	}

	/**
	 * @return string[string|int]
	 */
	public function matches() {
		return $this->matches;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->controller() . '::' . $this->action() . '() when ' . $this->pattern();
	}

}