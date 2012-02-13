<?php

class Nano_Route_RegExp extends Nano_Route {

	public function __construct($location, $controller, $action, $module) {
		$location = null === $location || 0 === strLen($location)
			? null
			: '/^' . $location . '$/i'
		;
		parent::__construct($location, $controller, $action, $module);
	}

	/**
	 * @return boolean
	 * @param string $location
	 */
	public function match($location) {
		$this->matches = array();

		return (1 === preg_match($this->location, $location, $this->matches));
	}

}