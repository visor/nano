<?php

namespace Nano;

abstract class Validator {

	/**
	 * @var string
	 */
	protected $message = null;

	/**
	 * @return boolean
	 * @param mixed $value
	 */
	abstract public function isValid($value);

	/**
	 * @return string
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * @return \Nano\Validator
	 * @param string $value
	 */
	public function setMessage($value) {
		$this->message = $value;
		return $this;
	}

}