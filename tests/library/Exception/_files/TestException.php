<?php

class Library_Exception_TestException extends \Nano\Exception {

	/**
	 * @return string
	 * @param mixed $value
	 */
	public function describe($value) {
		return $this->describeValue($value);
	}

}