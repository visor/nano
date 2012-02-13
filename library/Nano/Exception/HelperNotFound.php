<?php

class Nano_Exception_HelperNotFound extends Nano_Exception {

	public function __construct($name, $module = null) {
		$message = 'Helper ' . $name . ($module ? ' in module ' . $module : '') . ' not found';
		parent::__construct($message);
	}

}