<?php

class Application_Exception_PathNotFound extends Application_Exception {

	public function __construct($path) {
		parent::__construct('Path not found: ' . $path);
	}

}