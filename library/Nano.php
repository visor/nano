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
define('MODELS',      APP . DS . 'models');
define('LAYOUTS',     APP . DS . 'layouts');
define('VIEWS',       APP . DS . 'views');
define('HELPERS',     APP . DS . 'helpers');
define('PLUGINS',     APP . DS . 'plugins');
define('APP_LIB',     APP . DS . 'library');
define('MESSAGES',    APP . DS . 'messages');
define('PUBLIC_DIR',  ROOT . DS . 'public');
define('TESTS',       ROOT . DS . 'tests');

//require LIB . DS . 'Nano' . DS . 'Loader.php';
//require LIB . DS . 'Nano' . DS . 'Modules.php';

final class Nano {

	/**
	 * @var Nano
	 */
	private static $instance = null;

	/**
	 * @var Nano_Dispatcher
	 */
	private $dispatcher;

	/**
	 * @var Nano_Modules
	 */
	private $modules;

	/**
	 * @var Nano_Routes
	 */
	private $routes;

	/**
	 * @var Nano_Config
	 */
	private static $config = null;

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
	public static function run($url = null) {
		self::instance();
		if (!defined('SELENIUM_ENABLE')) {
			define(
				'SELENIUM_ENABLE'
				, self::config()->exists('selenium') && isSet(self::config('selenium')->enabled) && true === self::config('selenium')->enabled
			);
			if (SELENIUM_ENABLE) {
				TestUtils_WebTest::startCoverage();
			}
		}
		include(SETTINGS . DS . 'routes.php');
		if (null === $url) {
			$url = $_SERVER['REQUEST_URI'];
		}
		if (false !== strPos($url, '?')) {
			$url = subStr($url, 0, strPos($url, '?'));
		}
		if (self::config('web')->url && 0 === strPos($url, self::config('web')->url)) {
			$url = subStr($url, strLen(self::config('web')->url));
		}
		if (self::config('web')->index) {
			$url = preg_replace('/' . preg_quote(self::config('web')->index) . '$/', '', $url);
		}
		$url = rawUrlDecode($url);
		try {
			$result = self::instance()->dispatcher->dispatch(self::instance()->routes, $url);
			if (isset($_SERVER['REQUEST_METHOD']) && 'HEAD' === strToUpper($_SERVER['REQUEST_METHOD'])) {
				return;
			}
			echo $result;
		} catch (Exception $e) {
			if (SELENIUM_ENABLE) {
				TestUtils_WebTest::stopCoverage();
			}
			throw $e;
		}
		if (SELENIUM_ENABLE) {
			TestUtils_WebTest::stopCoverage();
		}
	}

	/**
	 * @return string
	 * @param Nano_Route $route
	 */
	public static function runRoute(Nano_Route $route) {
		return self::instance()->dispatcher->run($route);
	}

	/**
	 * @return Nano_Routes
	 */
	public static function routes() {
		return self::instance()->routes;
	}

	/**
	 * @return Nano_Modules
	 */
	public static function modules() {
		return self::instance()->modules;
	}

	/**
	 * @return Nano_Dispatcher
	 */
	public static function dispatcher() {
		return self::instance()->dispatcher;
	}

	/**
	 * @return Nano_HelperBroker
	 */
	public static function helper() {
		return Nano_HelperBroker::instance();
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

	private function __construct() {
		if ($this->config()->fileExists()) {
			$this->setupErrorReporting();
			if (self::config()->exists('web')) {
				define('WEB_ROOT', Nano::config('web')->root);
				define('WEB_URL',  Nano::config('web')->url);
			}
		}

		$this->modules    = new Nano_Modules();
		$this->dispatcher = new Nano_Dispatcher();
		$this->routes     = new Nano_Routes();
	}

	private function setupErrorReporting() {
		if (self::config()->exists('web') && isset(self::config('web')->errorReporting) && true === self::config('web')->errorReporting) {
			error_reporting(E_ALL | E_STRICT);
			ini_set('display_errors', true);
		} else {
			error_reporting(0);
			ini_set('display_errors', false);
		}
	}

	private function __clone() { throw new RuntimeException(); }
	private function __sleep() { throw new RuntimeException(); }
	private function __wakeUp() { throw new RuntimeException(); }

}

//function nano_autoload($className) {
//	return Nano_Loader::load($className);
//}

//Nano_Loader::initLibraries();
//spl_autoload_register('nano_autoload');