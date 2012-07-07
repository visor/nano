<?php

namespace Nano\Validator;

class Email extends \Nano\Validator {

	public function isValid($value) {
		if (false === filter_var($value, FILTER_VALIDATE_EMAIL)) {
			return false;
		}
		return true;
	}

}