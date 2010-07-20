<?php

class Nano_Validator_Required extends Nano_Validator {

	public function isValid($value) {
		return null !== $value;
	}

}