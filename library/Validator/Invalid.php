<?php

namespace Nano\Validator;

class Invalid extends \Nano\Validator {

	public function isValid($value) {
		return false;
	}

}