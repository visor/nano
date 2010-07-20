<?php

class Nano_Message_Plural_RU implements Nano_Message_Plural_Interface {

	/**
	 * @return string
	 * @param int $number
	 * @param string[] $variants
	 */
	public function get($number, array $variants) {
		$cases = array(2, 0, 1, 1, 1, 2);
		return $variants[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
	}

}