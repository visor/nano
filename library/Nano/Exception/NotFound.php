<?php

class Nano_Exception_NotFound extends Nano_Exception {

	/**
	 * @param string|null $message
	 * @param Nano_Route|null $route
	 */
	public function __construct($message = null, Nano_Route $route = null) {
		if ($route instanceof Nano_Route) {
			parent::__construct($message . '(route: ' . $route . ')');
		} else {
			parent::__construct($message);
		}
	}

}