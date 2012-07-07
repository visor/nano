<?php

namespace Nano\Application\Config;

class Exception extends \Nano\Exception {

	public function __construct($message, $code = 0, \Exception $previous = null) {
		parent::__construct('Configuration: ' . $message, $code, $previous);
	}

}