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

	public function __construct() {
		$this->nanoDir         = __DIR__;
		$this->baseIncludePath = explode(PATH_SEPARATOR, $this->nanoDir . PATH_SEPARATOR . trim(get_include_path(), PATH_SEPARATOR));
		spl_autoload_register(array($this, 'loadClass'));
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

		if (Util\Classes::isNanoClass($name)) {
			return $this->loadFileWithClass($name, Names::nanoFile($name));
		}
		if (Util\Classes::isApplicationClass($name)) {
			return $this->loadFileWithClass($name, Names::applicationFile($name));
		}
		if (Util\Classes::isModuleClass($name)) {
			return $this->loadFileWithClass($name, Names::moduleFile($name));
		}

		return false;
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

}