<?php

class Nano_Loader {

	const NAME_SEPARATOR = '_';

	/**
	 * @return string
	 * @param string $name
	 */
	public static function classToPath($name) {
		return str_replace(self::NAME_SEPARATOR, DS, $name) . '.php';
	}

}