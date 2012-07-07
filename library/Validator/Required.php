<?php

namespace Nano\Validator;

class Required extends \Nano\Validator {

	public function isValid($value) {
		return null !== $value;
	}

}