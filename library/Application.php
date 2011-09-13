<?php

class Application {

	const MODULES_DIR_NAME = 'modules';
	const PUBLIC_DIR_NAME  = 'public';

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
	public function withRootDir($value) {
		$this->rootDir = $value;
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
			//todo: check shared module
			//todo: check application module
			//todo: throw exception
		}
		if (!is_dir($path)) {
			throw new Application_Exception_PathNotFound($path);
		}
		if (!is_dir($path . DIRECTORY_SEPARATOR . $name)) {
			throw new Application_Exception_PathNotFound($path . DIRECTORY_SEPARATOR . $name);
		}

		$this->getModules()->append($name, $path);
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
	 * @param string $value
	 */
	public function usingConfigurationFormat($value) {
		$this->configFormat = Nano_Config::formatFactory($value);
		return $this;
	}

	public function start() {
		//todo: include required classes
		//todo: start dispatcher
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

	public function getConfigurationFormat() {
		if (null === $this->configFormat) {
			$this->usingConfigurationFormat('php');
		}
		return $this->configFormat;
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

}
