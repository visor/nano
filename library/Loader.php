<?php

namespace Nano;

class Loader {

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
		return \Nano\Modules::nameToNamespace($module) . NS . \Nano::stringToName($class);
	}

	/**
	 * @return boolean
	 * @param string $className
	 */
	public static function isModuleClass($className) {
		if (false === strPos($className, \Nano\Modules::MODULE_SUFFIX . NS)) {
			return false;
		}
		return true;
	}

	/**
	 * @return string[]
	 * @param string $className
	 */
	public static function extractModuleClassParts($className) {
		return explode(NS, trim($className, NS), 2);
	}

	public function __construct() {
		$this->nanoDir         = __DIR__;
		$this->baseIncludePath = explode(PATH_SEPARATOR, $this->nanoDir . PATH_SEPARATOR . trim(get_include_path(), PATH_SEPARATOR));
		spl_autoload_register(array($this, 'loadClass'));
		spl_autoload_register(array($this, 'loadCommonClass'));
	}

	/**
	 * Registers application directories for autoloading
	 *
	 * @return void
	 * @param \Nano\Application $application
	 */
	public function registerApplication(\Nano\Application $application) {
		$this->applicationPath = array(
			$this->nanoDir
			, $application->rootDir . DIRECTORY_SEPARATOR . \Nano\Application::CONTROLLER_DIR_NAME
			, $application->rootDir . DIRECTORY_SEPARATOR . \Nano\Application::LIBRARY_DIR_NAME
			, $application->rootDir . DIRECTORY_SEPARATOR . \Nano\Application::MODELS_DIR_NAME
			, $application->rootDir . DIRECTORY_SEPARATOR . \Nano\Application::PLUGINS_DIR_NAME
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
		$this->modulesPath[\Nano\Modules::nameToNamespace($name)] = array(
			$path . DIRECTORY_SEPARATOR . \Nano\Application::CONTROLLER_DIR_NAME
			, $path . DIRECTORY_SEPARATOR . \Nano\Application::LIBRARY_DIR_NAME
			, $path . DIRECTORY_SEPARATOR . \Nano\Application::MODELS_DIR_NAME
			, $path . DIRECTORY_SEPARATOR . \Nano\Application::PLUGINS_DIR_NAME
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
		if (class_exists($name, false)) {
			return true;
		}

		if (Classes::isNanoClass($name)) {
			return $this->loadFileWithClass($name, Names::nanoFile($name));
		}
		if (Classes::isApplicationClass($name)) {
			return $this->loadFileWithClass($name, Names::applicationFile($name));
		}
		if (Classes::isModuleClass($name)) {
			return $this->loadFileWithClass($name, Names::moduleFile($name));
		}

		//{{{ todo: deprecated. remove this
		if (self::isModuleClass($name)) {
			return $this->loadModuleClass($name);
		}
		if (null === $this->applicationPath) {
			return $this->loadCommonClass($name);
		}
		return $this->loadApplicationClass($name);
		//}}}
		//return false;
	}

	public function loadCommonClass($name) {
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
		} catch (\Exception $e) {
			$result = false;
		}
		return $result;
	}

}