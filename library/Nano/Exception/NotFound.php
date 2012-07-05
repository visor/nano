<?php

class Nano_Exception_NotFound extends \Nano\Exception {

	/**
	 * @param string|null $message
	 * @param Nano_Route_Abstract|null $route
	 */
	public function __construct($message = null, Nano_Route_Abstract $route = null) {
		if ($route instanceof Nano_Route_Abstract) {
			parent::__construct($message . ' (route: ' . $route . ')');
		} else {
			parent::__construct($message);
		}
	}

}