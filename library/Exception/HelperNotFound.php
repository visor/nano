<?php

namespace Nano\Exception;

class HelperNotFound extends \Nano\Exception {

	public function __construct($name, $module = null) {
		$message = 'Helper ' . $name . ($module ? ' in module ' . $module : '') . ' not found';
		parent::__construct($message);
	}

}