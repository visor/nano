<?php

define('DS', DIRECTORY_SEPARATOR);
define('NS', '\\');

final class Nano {

	/**
	 * @var \Nano\Application|null
	 */
	private static $app = null;

	/**
	 * @return \Nano\Application|null
	 */
	public static function app() {
		return self::$app;
	}

	/**
	 * @return null|string
	 * @param string $base
	 * @param string $id
	 * @param array|null $params
	 */
	public static function t($base, $id, array $params = null) {
		if (self::$app->locale instanceof \Nano\L10n\Locale) {
			return self::$app->locale->translate(null, $base, $id, $params);
		}
		return null;
	}

	/**
	 * @return null|string
	 * @param string $module
	 * @param string $base
	 * @param string $id
	 * @param array $params
	 */
	public static function tm($module, $base, $id, array $params = null) {
		if (self::$app->locale instanceof \Nano\L10n\Locale) {
			return self::$app->locale->translate($module, $base, $id, $params);
		}
		return null;
	}

	/**
	 * @return void
	 * @param \Nano\Application|null $app
	 * @throws \Nano\Exception
	 */
	public static function setApplication(\Nano\Application $app = null) {
		if (null === self::$app || null === $app) {
			self::$app = $app;
			return;
		}
		throw new \Nano\Exception('Application inctance already created');
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