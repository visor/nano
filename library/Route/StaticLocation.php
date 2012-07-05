<?php

namespace Nano\Route;

class StaticLocation extends Common {

	public function __construct($location, $controller, $action, $module, array $params = array()) {
		parent::__construct(mb_strToLower($location, 'UTF-8'), $controller, $action, $module, $params);
	}

	/**
	 * @return boolean
	 * @param string $location
	 */
	public function match($location) {
		return (mb_strToLower($location, 'UTF-8') === $this->location);
	}

}