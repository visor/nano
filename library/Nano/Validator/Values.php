<?php

class Nano_Validator_Values extends Nano_Validator {

	/**
	 * @var array
	 */
	protected $values;

	/**
	 * @param array $values
	 * @param string|null $message
	 */
	public function __construct(array $values, $message = null) {
		$this->values = $values;
		$this->setMessage($message);
	}

	/**
	 * @return boolean
	 * @param mixed $value
	 */
	public function isValid($value) {
		if (!isSet($this->values[$value])) {
			return false;
		}
		return true;
	}

}