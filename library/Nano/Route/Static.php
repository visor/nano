<?php

class Nano_Route_Static extends Nano_Route_Abstract {

	public function __construct($location, $controller, $action, $module, array $params = array()) {
		$location = mb_strToLower($location, 'UTF-8');
		parent::__construct($location, $controller, $action, $module, $params);
	}

	/**
	 * @return boolean
	 * @param string $location
	 */
	public function match($location) {
		return (mb_strToLower($location, 'UTF-8') === $this->location);
	}

}