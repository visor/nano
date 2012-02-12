<?php

//todo: move defines into application or nano start method
define('DS',          DIRECTORY_SEPARATOR);
define('PS',          PATH_SEPARATOR);
define('LIB',         __DIR__);
define('ROOT',        dirName(LIB));
define('APP',         ROOT . DS . 'application');
define('MODULES',     APP . DS . 'modules');
define('SETTINGS',    APP . DS . 'settings');
define('CONTROLLERS', APP . DS . 'controllers');
define('MESSAGES',    APP . DS . 'messages');
define('PUBLIC_DIR',  ROOT . DS . 'public');
define('TESTS',       ROOT . DS . 'tests');

final class Nano {

	/**
	 * @return boolean
	 */
	public static function isTesting() {
		return isset($_COOKIE['PHPUNIT_SELENIUM_TEST_ID']) || defined('TESTING');
	}

	/**
	 * @return Nano_Db
	 * @param string $name
	 */
	public static function db($name = null) {
		return Nano_Db::instance($name);
	}

	/**
	 * Converts given string into CamelCased class name
	 *
	 * @return string
	 * @param string $string
	 */
	public static function stringToName($string) {
		$result = strToLower($string);
		$result = str_replace('-', ' ', $result);
		$result = ucWords($result);
		$result = str_replace(' ', '', $result);
		$result = trim($result);
		return $result;
	}

}