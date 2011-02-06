<?php

class Nano_Config {

	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @var array
	 */
	protected $config = null;

	public function __construct($path) {
		$this->setPath($path);
	}

	/**
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * @return void
	 * @param string $value
	 */
	public function setPath($value) {
		$this->path   = $value;
		$this->config = null;
	}

	/**
	 * @return boolean
	 * @param string $name
	 */
	public function exists($name) {
		$this->load();
		return isSet($this->config->$name);
	}

	/**
	 * @return mixed
	 * @param string $name
	 */
	public function get($name) {
		if ($this->exists($name)) {
			return $this->config->$name;
		}
		return null;
	}

	/**
	 * @return void
	 * @param string $name
	 * @param mixed $value
	 */
	public function set($name, $value) {
		$this->load();
		$this->config->$name = $value;
	}

	protected function load() {
		if (null === $this->config) {
			if (!file_exists($this->path)) {
				throw new Nano_Exception('File "' . $this->path . '" not found');
			}
			if (!is_readable($this->path)) {
				throw new Nano_Exception('Cannot read file "' . $this->path . '"');
			}
			$this->config = include($this->path);
		}
	}

}