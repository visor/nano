<?php

namespace Nano\Application\Exception;

class ModuleNotFound extends \Nano\Application\Exception {

	public function __construct($name) {
		parent::__construct('Module ' . $this->describeValue($name) . ' not found in application and shared modules');
	}

}