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
	 * @var Nano
	 */
	private static $instance = null;

	/**
	 * @var Nano_Config
	 */
	private static $config = null;

	/**
	 * @var Nano_HelperBroker
	 */
	private static $helper = null;

	/**
	 * @return Nano
	 */
	public static function instance() {
		if (null == self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * @return void
	 * @param Nano_Config $config
	 */
	public static function configure(Nano_Config $config) {
		self::$config = $config;
	}

	/**
	 * @return void
	 * @param string $url
	 */
//	public static function run($url = null) {
//		self::instance();
//		if (!defined('SELENIUM_ENABLE')) {
//			define(
//				'SELENIUM_ENABLE'
//				, self::config()->exists('selenium') && isSet(self::config('selenium')->enabled) && true === self::config('selenium')->enabled
//			);
//			if (SELENIUM_ENABLE) {
//				TestUtils_WebTest::startCoverage();
//			}
//		}
//		include(SETTINGS . DS . 'routes.php');
//		if (null === $url) {
//			$url = $_SERVER['REQUEST_URI'];
//		}
//		if (false !== strPos($url, '?')) {
//			$url = subStr($url, 0, strPos($url, '?'));
//		}
//		if (self::config('web')->url && 0 === strPos($url, self::config('web')->url)) {
//			$url = subStr($url, strLen(self::config('web')->url));
//		}
//		if (self::config('web')->index) {
//			$url = preg_replace('/' . preg_quote(self::config('web')->index) . '$/', '', $url);
//		}
//		$url = rawUrlDecode($url);
//		try {
//			$result = self::instance()->dispatcher->dispatch(self::instance()->routes, $url);
//			if (isset($_SERVER['REQUEST_METHOD']) && 'HEAD' === strToUpper($_SERVER['REQUEST_METHOD'])) {
//				return;
//			}
//			echo $result;
//		} catch (Exception $e) {
//			if (SELENIUM_ENABLE) {
//				TestUtils_WebTest::stopCoverage();
//			}
//			throw $e;
//		}
//		if (SELENIUM_ENABLE) {
//			TestUtils_WebTest::stopCoverage();
//		}
//	}

	/**
	 * @return Nano_Routes
	 */
	public static function routes() {
		return self::config()->routes();
	}

	/**
	 * @return Nano_HelperBroker
	 */
	public static function helper() {
		if (null === self::$helper) {
			self::$helper = new Nano_HelperBroker(Application::current());
		}
		return self::$helper;
	}

	/**
	 * @return Nano_Config|mixed
	 * @param string $name
	 */
	public static function config($name = null) {
		if (null === self::$config) {
			throw new RuntimeException('Application not configred');
		}
		if (null === $name) {
			return self::$config;
		}
		return self::$config->get($name);
	}

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
	 * @return Nano_Message
	 */
	public static function message() {
		return Nano_Message::instance();
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

	private function __construct() {}

	private function __clone() { throw new RuntimeException(); }
	private function __sleep() { throw new RuntimeException(); }
	private function __wakeUp() { throw new RuntimeException(); }

}