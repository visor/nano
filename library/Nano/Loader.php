<?php

class Nano_Loader {

	const LIBRARY_DIR          = 'library';
	const NAME_SEPARATOR       = '_';

	/**
	 * @var null|string
	 */
	protected $nanoDir         = null;

	/**
	 * @var array|null
	 */
	protected $baseIncludePath = null;

	/**
	 * @var array|null
	 */
	protected $applicationPath = null;

	/**
	 * @var array|null
	 */
	protected $modulesPath     = array();

	/**
	 * @return string
	 * @param string $name
	 */
	public static function classToPath($name) {
		return str_replace(self::NAME_SEPARATOR, DIRECTORY_SEPARATOR, $name) . '.php';
	}

	/**
	 * @return string
	 * @param string $module
	 * @param string $class
	 */
	public static function formatModuleClassName($module, $class) {
		return Nano_Modules::nameToNamespace($module) . '\\' . Nano::stringToName($class);
	}

	/**
	 * @return boolean
	 * @param string $className
	 */
	public static function isModuleClass($className) {
		if (false === strPos($className, Nano_Modules::MODULE_SUFFIX . '\\')) {
			return false;
		}
		return true;
	}

	/**
	 * @return string[]
	 * @param string $className
	 */
	public static function extractModuleClassParts($className) {
		return explode('\\', trim($className, '\\'), 2);
	}

	public function __construct() {
		$this->nanoDir         = dirName(__DIR__);
		$this->baseIncludePath = explode(PATH_SEPARATOR, $this->nanoDir . PATH_SEPARATOR . trim(get_include_path(), PATH_SEPARATOR));
		spl_autoload_register(array($this, 'loadClass'));
		spl_autoload_register(array($this, 'loadCommonClass'));
	}

	/**
	 * Registers application directories for autoloading
	 *
	 * @return void
	 * @param Application $application
	 */
	public function registerApplication(Application $application) {
		$this->applicationPath = array(
			$this->nanoDir
			, $application->rootDir . DIRECTORY_SEPARATOR . Application::CONTROLLER_DIR_NAME
			, $application->rootDir . DIRECTORY_SEPARATOR . Application::LIBRARY_DIR_NAME
			, $application->rootDir . DIRECTORY_SEPARATOR . Application::MODELS_DIR_NAME
			, $application->rootDir . DIRECTORY_SEPARATOR . Application::PLUGINS_DIR_NAME
		);
	}

	/**
	 * Registers module directories for autoloading
	 *
	 * @return void
	 * @param string $name
	 * @param string $path
	 */
	public function registerModule($name, $path) {
		$this->modulesPath[Nano_Modules::nameToNamespace($name)] = array(
			$path . DIRECTORY_SEPARATOR . Application::CONTROLLER_DIR_NAME
			, $path . DIRECTORY_SEPARATOR . Application::LIBRARY_DIR_NAME
			, $path . DIRECTORY_SEPARATOR . Application::MODELS_DIR_NAME
			, $path . DIRECTORY_SEPARATOR . Application::PLUGINS_DIR_NAME
		);
	}

	/**
	 * Tries to load require class from file
	 *
	 * @return boolean
	 * @param string $className
	 * @param string $fileName
	 */
	public function loadFileWithClass($className, $fileName) {
		if (!file_exists($fileName)) {
			return false;
		}
		if (false === include($fileName)) {
			return false;
		}
		if (!class_exists($className, false)) {
			return false;
		}
		return true;
	}

	/**
	 * Tries to find and include $className
	 *
	 * @return boolean
	 * @param string $name
	 */
	public function loadClass($name) {
		try {
			if (class_exists($name, false)) {
				return true;
			}
			if (self::isModuleClass($name)) {
				return $this->loadModuleClass($name);
			}
			if (null === $this->applicationPath) {
				return $this->loadCommonClass($name);
			}
			return $this->loadApplicationClass($name);
		} catch (Exception $e) {
			return false;
		}
	}

	public function loadCommonClass($name) {
//		echo $name, PHP_EOL;
		return $this->loadWithIncludePath($name, $this->baseIncludePath);
	}

	/**
	 * @return boolean
	 * @param string $name
	 */
	protected function loadApplicationClass($name) {
		return $this->loadWithIncludePath($name, $this->applicationPath);
	}

	protected function loadModuleClass($name) {
		list($namespace, $className) = self::extractModuleClassParts($name);
		if (!isSet($this->modulesPath[$namespace])) {
			return false;
		}
		return $this->loadWithIncludePath($className, $this->modulesPath[$namespace]);
	}

	public function loadWithIncludePath($className, array $directories) {
		$result = true;
		try {
			$fileName = self::classToPath($className);
			foreach ($directories as $path) {
				$testFile = $path . DIRECTORY_SEPARATOR . $fileName;
				if (!file_exists($testFile)) {
					continue;
				}
				if (false === include($testFile)) {
					$result = false;
				}
				break;
			}
		} catch (Exception $e) {
			$result = false;
		}
		return $result;
	}

}