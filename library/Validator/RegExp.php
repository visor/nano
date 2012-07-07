<?php

namespace Nano\Validator;

class RegExp extends \Nano\Validator {

	/**
	 * @var string
	 */
	protected $pattern;

	/**
	 * @param string $pattern
	 */
	function __construct($pattern) {
		$this->pattern = $pattern;
	}

	/**
	 * @return bool
	 * @param mixed $value
	 */
	public function isValid($value) {
		$matchResult = preg_match($this->pattern, $value);
		if (0 === $matchResult || false === $matchResult) {
			return false;
		}
		return true;
	}

}