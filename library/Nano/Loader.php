<?php

class Nano_Loader {

	const NAME_SEPARATOR = '_';

	/**
	 * @return boolean
	 * @param string $className
	 */
	public static function load($className) {
		try {
			if (false === include(self::classToPath($className))) {
				return false;
			}
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	public static function initLibraries() {
		set_include_path(
			LIB
			. PS . APP_LIB
			. PS . MODELS
			. PS . CONTROLLERS
			. PS . PLUGINS
			. PS . HELPERS
			. PS . get_include_path()
		);
	}

	/**
	 * @return string
	 * @param string $name
	 */
	public static function classToPath($name) {
		return str_replace(self::NAME_SEPARATOR, DS, $name) . '.php';
	}

}