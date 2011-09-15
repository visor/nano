<?php

class Nano_Loader {

	const LIBRARY_DIR           = 'library';
	const NAME_SEPARATOR        = '_';

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
		return explode('\\', $className, 2);
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
			(null === $application ? dirName(dirName(__DIR__)) : $application->getNanoRootDir())
			. DIRECTORY_SEPARATOR . self::LIBRARY_DIR
		;
		$this->useDirectory($nanoDir);
		if (!class_exists('Nano', false)) {
			$this->loadCommonClass('Nano');
			$this->loadCommonClass('Nano_Modules');
			$this->loadCommonClass('Nano_Log');
		}
	}

	/**
	 * Appends given $path into PHP include_path
	 *
	 * @return Nano_Loader
	 * @param string $path
	 */
	public function useDirectory($path) {
		set_include_path($path . PATH_SEPARATOR . get_include_path());
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
	 * @param string $name
	 */
	protected function loadCommonClass($name) {
		if (false === include($this->classToPath($name))) {
			return false;
		}
		return true;
	}

}