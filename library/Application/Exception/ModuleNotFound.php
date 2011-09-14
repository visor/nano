<?php

class Application_Exception_ModuleNotFound extends Application_Exception {

	public function __construct($name) {
		parent::__construct('Module ' . $this->describeValue($name) . ' not found in application and shared modules');
	}

}