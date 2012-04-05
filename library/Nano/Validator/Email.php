<?php

class Nano_Validator_Email extends Nano_Validator {

	public function isValid($value) {
		if (false === filter_var($value, FILTER_VALIDATE_EMAIL)) {
			return false;
		}
		return true;
	}

}