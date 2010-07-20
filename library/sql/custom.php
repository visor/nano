<?php

class sql_custom {

	/**
	 * @var string
	 */
	protected $value = null;

	/**
	 * @param string $value
	 */
	public function __construct($value) {
		$this->value = $value;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->value;
	}

}