<?php

class Nano_Exception_UnsupportedConfigFormat extends \Nano\Exception {

	public function __construct($name) {
		parent::__construct('Unsupported format: ' . $name);
	}

}