<?php

class Application {

	const PUBLIC_DIR_NAME     = 'public';
	const MODULES_DIR_NAME    = 'modules';
	const CONTROLLER_DIR_NAME = 'controllers';
	const LIBRARY_DIR_NAME    = 'library';
	const MODELS_DIR_NAME     = 'models';
	const HELPERS_DIR_NAME    = 'helpers';
	const PLUGINS_DIR_NAME    = 'plugins';

	/**
	 * @var null|Application
	 */
	private static $current = null;

	/**
	 * @var string|null
	 */
	protected
		$rootDir            = null
		, $publicDir        = null
		, $modulesDir       = null
		, $sharedModulesDir = null
		, $nanoRootDir      = null
	;

	/**
	 * @var Nano_Config_Format
	 */
	protected $configFormat = null;

	/**
	 * @var Nano_Modules|null
	 */
	protected $modules = null;

	/**
	 * @var SplObjectStorage
	 */
	protected $plugins = null;

	/**
	 * @var Nano_Loader
	 */
	protected $loader = null;

	/**
	 * @var Nano_Dispatcher
	 */
	protected $dispatcher;

	/**
	 * @var Event_Manager
	 */
	protected $eventManager = null;

	/**
	 * @return Application
	 */
	public static function create() {
		$result        = new self();
		self::$current = $result;
		return $result;
	}

	/**
	 * @return Application|null
	 */
	public static function current() {
		return self::$current;
	}

	/**
	 * @return Application
	 * @param string $value
	 */
	public function usingConfigurationFormat($value) {
		$this->configFormat = Nano_Config::formatFactory($value);
		return $this;
	}

	/**
	 * @return Application
	 * @param string $value
	 */
	public function withRootDir($value) {
		$this->rootDir = $value;
		$this->loader()
			->useDirectory($this->rootDir . DIRECTORY_SEPARATOR . self::CONTROLLER_DIR_NAME)
			->useDirectory($this->rootDir . DIRECTORY_SEPARATOR . self::LIBRARY_DIR_NAME)
			->useDirectory($this->rootDir . DIRECTORY_SEPARATOR . self::MODELS_DIR_NAME)
			->useDirectory($this->rootDir . DIRECTORY_SEPARATOR . self::HELPERS_DIR_NAME)
			->useDirectory($this->rootDir . DIRECTORY_SEPARATOR . self::PLUGINS_DIR_NAME)
		;
		return $this;
	}

	/**
	 * @return Application
	 * @param string $value
	 */
	public function withPublicDir($value) {
		$this->publicDir = $value;
		return $this;
	}

	/**
	 * @return Application
	 * @param string $value
	 */
	public function withModulesDir($value) {
		$this->modulesDir = $value;
		return $this;
	}

	/**
	 * @return Application
	 * @param string $value
	 */
	public function withSharedModulesDir($value) {
		$this->sharedModulesDir = $value;
		return $this;
	}

	/**
	 * @return Application
	 * @param string $name
	 * @param string $path
	 */
	public function withModule($name, $path = null) {
		if (null === $path) {
			if (is_dir($this->getSharedModulesDir() . DIRECTORY_SEPARATOR . $name)) {
				$path = $this->getSharedModulesDir() . DIRECTORY_SEPARATOR . $name;
			} elseif (is_dir($this->getModulesDir() . DIRECTORY_SEPARATOR . $name)) {
				$path = $this->getModulesDir() . DIRECTORY_SEPARATOR . $name;
			} else {
				throw new Application_Exception_ModuleNotFound($name);
			}
		}
		$this->getModules()->append($name, $path);
		$this->loader()
			->useDirectory($path . DIRECTORY_SEPARATOR . self::CONTROLLER_DIR_NAME)
			->useDirectory($path . DIRECTORY_SEPARATOR . self::LIBRARY_DIR_NAME)
			->useDirectory($path . DIRECTORY_SEPARATOR . self::MODELS_DIR_NAME)
			->useDirectory($path . DIRECTORY_SEPARATOR . self::HELPERS_DIR_NAME)
			->useDirectory($path . DIRECTORY_SEPARATOR . self::PLUGINS_DIR_NAME)
		;
		return $this;
	}

	/**
	 * @return Application
	 * @param Nano_C_Plugin $value
	 */
	public function withPlugin(Nano_C_Plugin $value) {
		$this->getPlugins()->attach($value);
		return $this;
	}

	/**
	 * @return Application
	 */
	public function configure() {
		Nano_Config::setFormat($this->getConfigurationFormat());
		Nano::configure(new Nano_Config($this->getRootDir() . DIRECTORY_SEPARATOR . 'settings'));
		$this->setupErrorReporting();

		define('APP_ROOT', Application::current()->getRootDir());
		define('WEB_ROOT', Application::current()->getPublicDir());
		return $this;
	}

	public function start() {
		//todo: detect request uri
		//todo: start dispatcher
		//todo: ??? handle head method
		$urlPrefix = Nano::config('web')->url;
		$url       = $_SERVER['REQUEST_URI'];
		if (false !== strPos($url, '?')) {
			$url = subStr($url, 0, strPos($url, '?'));
		}
		if ($urlPrefix && 0 === strPos($url, $urlPrefix)) {
			$url = subStr($url, strLen($urlPrefix));
		}
		if (Nano::config('web')->index) {
			$url = preg_replace('/' . preg_quote(Nano::config('web')->index) . '$/', '', $url);
		}
		$url = trim(rawUrlDecode($url), '/');
		$result = $this->dispatcher->dispatch(Nano::routes(), $url);
		if (isset($_SERVER['REQUEST_METHOD']) && 'HEAD' === strToUpper($_SERVER['REQUEST_METHOD'])) {
			return;
		}
		echo $result;
	}

	/**
	 * @return Nano_Config_Format
	 */
	public function getConfigurationFormat() {
		if (null === $this->configFormat) {
			$this->usingConfigurationFormat('php');
		}
		return $this->configFormat;
	}

	/**
	 * @return string
	 */
	public function getRootDir() {
		if (null === $this->rootDir) {
			$this->withRootDir(getCwd());
		}
		return $this->rootDir;
	}

	/**
	 * @return srting
	 */
	public function getPublicDir() {
		if (null === $this->publicDir) {
			$this->withPublicDir($this->getRootDir() . DIRECTORY_SEPARATOR . self::PUBLIC_DIR_NAME);
		}
		return $this->publicDir;
	}

	/**
	 * @return srting
	 */
	public function getModulesDir() {
		if (null === $this->modulesDir) {
			$this->withModulesDir($this->getRootDir() . DIRECTORY_SEPARATOR . self::MODULES_DIR_NAME);
		}
		return $this->modulesDir;
	}

	/**
	 * @return srting
	 */
	public function getSharedModulesDir() {
		if (null === $this->sharedModulesDir) {
			$this->withSharedModulesDir($this->getNanoRootDir() . DIRECTORY_SEPARATOR . self::MODULES_DIR_NAME);
		}
		return $this->sharedModulesDir;
	}

	/**
	 * @return Nano_Modules
	 */
	public function getModules() {
		if (null === $this->modules) {
			$this->modules = new Nano_Modules();
		}
		return $this->modules;
	}

	/**
	 * @return Nano_C_Plugin[]|SplObjectStorage
	 */
	public function getPlugins() {
		if (null === $this->plugins) {
			$this->plugins = new SplObjectStorage();
		}
		return $this->plugins;
	}

	/**
	 * @return string
	 */
	public function getNanoRootDir() {
		if (null === $this->nanoRootDir) {
			$this->nanoRootDir = dirName(__DIR__);
		}
		return $this->nanoRootDir;
	}

	public function getDispatcher() {
		return $this->dispatcher;
	}

	/**
	 * @return Nano_Loader
	 */
	public function loader() {
		return $this->loader;
	}

	public function eventManager() {
		return $this->eventManager;
	}

	public function __construct() {
		require_once __DIR__ . DIRECTORY_SEPARATOR . 'Nano' . DIRECTORY_SEPARATOR . 'Loader.php';

		$this->loader = new Nano_Loader();
		$this->loader->register($this);

		$this->dispatcher   = new Nano_Dispatcher($this);
		$this->eventManager = new Event_Manager();
	}

	protected function setupErrorReporting() {
		if (Nano::config()->exists('web') && isSet(Nano::config('web')->errorReporting) && true === Nano::config('web')->errorReporting) {
			error_reporting(E_ALL | E_STRICT);
			ini_set('display_errors', true);
		} else {
			error_reporting(0);
			ini_set('display_errors', false);
		}
	}
}
