<?php

class Nano_Modules extends ArrayObject {

	/**
	 * @return Nano_Modules
	 * @param string $name
	 * @param string $path
	 */
	public function append($name, $path = null) {
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
	public function offsetSet($name, $path = null) {
		if (null === $path) {
			$path = MODULES . DS . $name;
		}
		parent::offsetSet($name, $path);
		Nano_Loader::initModuleLibraries($this, $name);
	}

}