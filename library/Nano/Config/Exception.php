<?php

class Nano_Config_Exception extends Nano_Exception {

	public function __construct($message, $code = 0, Exception $previous = null) {
		parent::__construct('Configuration: ' . $message, $code, $previous);
	}

}