<?php

interface Nano_Validator_Interface {

	/**
	 * @return boolean
	 * @param mixed $value
	 */
	public function isValid($value);

	/**
	 * @return string
	 */
	public function getMessage();

}