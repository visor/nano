<?php

class TestUtils_Constraint_NoException extends PHPUnit_Framework_Constraint {

	/**
	 * @return bool
	 * @param mixed $other Value or object to evaluate.
	 */
	public function evaluate($other) {
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

	protected function customFailureDescription($other, $description, $not) {
		return $this->toString();
	}

}