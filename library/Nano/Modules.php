<?php

/**
 * todo: rename to application_modules?
 */
class Nano_Modules extends ArrayObject {

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
	 * @return Nano_Modules
	 * @param string $name
	 */
	public function remove($name) {
		$this->offsetUnset($name);
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
			return null;
		}
		$result = $this->offsetGet($name);
		if (null === $folder) {
			return $result;
		}
		return $result . DS . $folder;
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