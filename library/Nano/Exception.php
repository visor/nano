<?php

class Nano_Exception extends RuntimeException {

	/**
	 * @return string
	 * @param mixed $value
	 */
	protected function describeValue($value) {
		if (is_object($value)) {
			return get_class($value);
		}
		if (is_null($value)) {
			return 'NULL';
		}
		return var_export($value, true);
	}

}