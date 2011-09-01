<?php

class TestUtils_Constraint_NoException extends PHPUnit_Framework_Constraint {

	public function __construct() {
	}

	/**
	 * @return boolean
	 * @param Closure $other
	 */
	protected function matches($other) {
		try {
			$other();
		} catch (Exception $e) {
			return false;
		}
		return true;
	}

	/**
	 * @return string
	 */
	public function toString() {
		return 'no exception should throw';
	}

	/**
	 * @return string
	 * @param  mixed $other Evaluated value or object.
	 */
	protected function failureDescription($other) {
		return $this->toString();
	}

}