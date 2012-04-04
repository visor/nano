<?php

class Nano_Validator_Date extends Nano_Validator_RegExp {

	protected $format;

	public function __construct($format) {
		$this->format = $format;

	}

	/**
	 * @return boolean
	 * @param mixed $value
	 */
	public function isValid($value) {
		try {
			return $value === Date::createFromFormat($this->format, $value)->format($this->format);
		} catch (Exception $e) {
			return false;
		}
	}

}