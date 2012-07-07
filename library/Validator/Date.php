<?php

namespace Nano\Validator;

class Nano_Validator_Date extends RegExp {

	protected $format;

	protected $required;

	public function __construct($format, $required = true) {
		$this->format   = $format;
		$this->required = $required;
	}

	/**
	 * @return boolean
	 * @param mixed $value
	 */
	public function isValid($value) {
		if (null === $value) {
			return !$this->required;
		}
		try {
			return $value === \Date::createFromFormat($this->format, $value)->format($this->format);
		} catch (\Exception $e) {
			return false;
		}
	}

}