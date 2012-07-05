<?php

namespace Nano;

/**
 * todo: rename to application_modules?
 */
class Modules extends \ArrayObject {

	const MODULE_NAME_SEPARATOR = '-';
	const MODULE_SUFFIX         = '_Module';

	/**
	 * @return string
	 * @param string $name
	 */
	public static function nameToNamespace($name) {
		return \Nano\Names::common($name);
	}

	/**
	 * @return string
	 * @param string $namespace
	 */
	public static function namespaceToName($namespace) {
		$result = preg_replace('/' . preg_quote(self::MODULE_SUFFIX) . '$/', '', $namespace);
		$result = preg_replace('/(.)([A-Z])/', '\\1' . self::MODULE_NAME_SEPARATOR . '\\2', $result);
		$result = strToLower($result);
		return $result;
	}

	/**
	 * @return \Nano\Modules
	 * @param string $name
	 * @param string $path
	 *
	 * @throws \Nano\Application\Exception\ModuleNotFound
	 */
	public function append($name, $path = null) {
		if (null === $path) {
			throw new \Nano\Application\Exception\ModuleNotFound($name);
		}
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
	 *
	 * @throws \Nano\Application\Exception\ModuleNotFound
	 */
	public function getPath($name, $folder = null) {
		if (!$this->offsetExists($name)) {
			throw new \Nano\Application\Exception\ModuleNotFound($name);
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
	 *
	 * @throws \Nano\Application\Exception\InvalidModuleNamespace
	 */
	public function nameToFolder($module) {
		if ($this->offsetExists($module)) {
			return $module;
		}

		return self::namespaceToName($module);
	}

	/**
	 * @return \Nano\Modules
	 * @param string $name
	 * @param string $path
	 *
	 * @throws \Nano\Application\Exception\PathNotFound
	 */
	public function offsetSet($name, $path) {
		if (!is_dir($path)) {
			throw new \Nano\Application\Exception\PathNotFound($path);
		}
		parent::offsetSet($name, $path);
	}

}