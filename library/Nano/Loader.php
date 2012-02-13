<?php

class Nano_Loader {

	const LIBRARY_DIR           = 'library';
	const NAME_SEPARATOR        = '_';

	protected $baseIncludePath   = '';
	protected $loadedIncludePath = array();

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
		$this->baseIncludePath = trim(get_include_path(), PATH_SEPARATOR);
	}

	/**
	 * Registers self instance as spl __autoload implementations
	 *
	 * @return void
	 * @param Application $application
	 */
	public function register(Application $application = null) {
		spl_autoload_register(array($this, 'loadClass'));
		$nanoDir =
			(null === $application ? dirName(dirName(__DIR__)) : $application->nanoRootDir)
			. DIRECTORY_SEPARATOR . self::LIBRARY_DIR
		;
		$this->useDirectory($nanoDir);
	}

	/**
	 * Appends given $path into PHP include_path
	 *
	 * @return Nano_Loader
	 * @param string $path
	 */
	public function useDirectory($path) {
		if (isSet($this->loadedIncludePath[$path])) {
			return $this;
		}

		$this->loadedIncludePath[$path] = $path;
		set_include_path(
			implode(PATH_SEPARATOR, $this->loadedIncludePath)
			. PATH_SEPARATOR . $this->baseIncludePath
		);
		return $this;
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
				list(, $className) = self::extractModuleClassParts($name);
				if (false === include(self::classToPath($className))) {
					return false;
				}
				return true;
			}

			return $this->loadCommonClass($name);
		} catch (Exception $e) {
			return false;
		}
	}

	/**
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
	 * @return boolean
	 * @param string $name
	 */
	protected function loadCommonClass($name) {
		if (false === include(self::classToPath($name))) {
			return false;
		}
		return true;
	}

}