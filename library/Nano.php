<?php

define('DS', DIRECTORY_SEPARATOR);
define('NS', '\\');

final class Nano {

	/**
	 * @var Application|null
	 */
	private static $app = null;

	/**
	 * @return Application|null
	 */
	public static function app() {
		return self::$app;
	}

	/**
	 * @return void
	 * @param Application|null $app
	 * @throws Nano_Exception
	 */
	public static function setApplication(Application $app = null) {
		if (null === self::$app || null === $app) {
			self::$app = $app;
			return;
		}
		throw new Nano_Exception('Application inctance already created');
	}

	/**
	 * @return boolean
	 */
	public static function isTesting() {
		return isset($_COOKIE['PHPUNIT_SELENIUM_TEST_ID']) || defined('TESTING');
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