<?php

namespace Nano\Exception;

class NotFound extends \Nano\Exception {

	/**
	 * @param string|null $message
	 * @param \Nano\Route\Common|null $route
	 */
	public function __construct($message = null, \Nano\Route\Common $route = null) {
		if ($route instanceof \Nano\Route\Common) {
			parent::__construct($message . ' (route: ' . $route . ')');
		} else {
			parent::__construct($message);
		}
	}

}