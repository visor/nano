<?php

class Nano_Validator_Error extends Exception {

	const UNKNOWN_ERROR = 'Неизвестная ошибка';

	/**
	 * @return Nano_Validator_Error
	 * @param string $code
	 */
	public function __construct($code) {
		$constant = get_class($this) . '::' . $code;
		if (defined($constant)) {
			parent::__construct(constant($constant));
		} else {
			parent::__construct(self::UNKNOWN_ERROR);
		}
	}

}