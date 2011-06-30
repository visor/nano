<?php

class Nano_Route_Static extends Nano_Route {

	public function __construct($location, $controller, $action, $module) {
		$location = mb_strToLower($location, 'UTF-8');
		parent::__construct($location, $controller, $action, $module);
		$this->matches = array();
	}

	/**
	 * @return boolean
	 * @param string $location
	 */
	public function match($location) {
		return (mb_strToLower($location, 'UTF-8') === $this->location);
	}

}