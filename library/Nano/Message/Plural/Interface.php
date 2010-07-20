<?php

interface Nano_Message_Plural_Interface {

	/**
	 * @return string
	 * @param int $number
	 * @param string[] $variants
	 */
	public function get($number, array $variants);

}