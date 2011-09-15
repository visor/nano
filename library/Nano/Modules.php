<?php

/**
 * todo: rename to application_modules?
 */
class Nano_Modules extends ArrayObject {

	const MODULE_NAME_SEPARATOR = '-';
	const MODULE_SUFFIX         = '_Module';

	/**
	 * Checks that given $name is valid module name
	 *
	 * @return boolean
	 * @param string $name
	 */
	public static function isModuleName($name) {
		$position = strPos($name, Nano_Modules::MODULE_SUFFIX);
		if (false === $position) {
			return false;
		}
		if ($position !== strLen($name) - strLen(Nano_Modules::MODULE_SUFFIX)) {
			return false;
		}
		return true;
	}

	/**
	 * @return string
	 * @param string $name
	 */
	public static function nameToNamespace($name) {
		return Nano::stringToName($name) . self::MODULE_SUFFIX;
	}

	/**
	 * @return Nano_Modules
	 * @param string $name
	 * @param string $path
	 */
	public function append($name, $path) {
		$this->offsetSet($name, $path);
		return $this;
	}

	/**
	 * @return boolean
	 * @param string $name
	 */
	public function active($name) {
		return $this->offsetExists($name);
	}

	/**
	 * @return string
	 * @param string $name
	 * @param string $folder
	 */
	public function getPath($name, $folder = null) {
		if (!$this->offsetExists($name)) {
			throw new Application_Exception_ModuleNotFound($name);
		}
		$result = $this->offsetGet($name);
		if (null === $folder) {
			return $result;
		}
		return $result . DS . $folder;
	}

	/**
	 * @return string
	 * @param string $module
	 */
	public function nameToFolder($module) {
		if ($this->offsetExists($module)) {
			return $module;
		}
		if (!self::isModuleName($module)) {
			throw new Application_Exception_InvalidModuleNamespace($module);
		}

		$result = preg_replace('/' . preg_quote(self::MODULE_SUFFIX) . '$/', '', $module);
		$result = preg_replace('/(.)([A-Z])/', '\\1' . self::MODULE_NAME_SEPARATOR . '\\2', $result);
		$result = strToLower($result);
		return $result;
	}

	/**
	 * @return Nano_Modules
	 * @param string $name
	 * @param string $path
	 */
	public function offsetSet($name, $path) {
		if (!is_dir($path)) {
			throw new Application_Exception_PathNotFound($path);
		}
		parent::offsetSet($name, $path);
	}

}