<?php

abstract class Nano_Validator implements Nano_Validator_Interface {

	/**
	 * @var string
	 */
	protected $message = null;

	/**
	 * @return string
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * @return Nano_Validator
	 * @param string $value
	 */
	public function setMessage($value) {
		$this->message = $value;
		return $this;
	}

}