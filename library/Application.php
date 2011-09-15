<?php

class Application {

	const PUBLIC_DIR_NAME     = 'public';
	const MODULES_DIR_NAME    = 'modules';
	const CONTROLLER_DIR_NAME = 'controllers';
	const LIBRARY_DIR_NAME    = 'library';
	const MODELS_DIR_NAME     = 'models';
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

		, $configFormat     = null
	;

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
	 * @return Nano_Application
	 */
	public static function configure() {
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

	public function start() {
		//todo: include required classes
		//todo: get application routes
		//todo: detect request uri
		//todo: start dispatcher
		//todo: ??? handle head method
	}

	/**
	 * @return string
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

	/**
	 * @return Nano_Loader
	 */
	public function loader() {
		return $this->loader;
	}

	public function __construct() {
		$this->loader = new Nano_Loader();
		$this->loader->register($this);
	}

}
