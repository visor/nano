<?php

class Nano_Config {

	const CONFIG_FILE_NAME  = 'configuration';
	const ROUTES_FILE_NAME  = 'routes';
	const CHANGED_FILE_NAME = 'changed';

	/**
	 * @var Nano_Config_Format
	 */
	protected  $format = null;

	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @var array
	 */
	protected $config = null;

	/**
	 * @var Nano_Routes
	 */
	protected $routes = null;

	/**
	 * @return Nano_Config_Format
	 * @param string $name
	 */
	public static function format($name) {
		/** @var Nano_Config_Format $result */
		$className = __CLASS__ . '_Format_' . ucFirst(strToLower($name));
		if (false === class_exists($className)) {
			throw new Nano_Exception_UnsupportedConfigFormat($name);
		}

		$class = new ReflectionClass($className);
		if (!$class->implementsInterface('Nano_Config_Format') || !$class->isInstantiable()) {
			throw new Nano_Exception_UnsupportedConfigFormat($name);
		}

		$result = $class->newInstance();
		if (!$result->available()) {
			throw new Nano_Exception_UnsupportedConfigFormat($name);
		}

		return $result;
	}

	/**
	 * @param string $path
	 * @param Nano_Config_Format $format
	 *
	 * @throws Nano_Exception_UnsupportedConfigFormat
	 */
	public function __construct($path, Nano_Config_Format $format) {
		if (false === $format->available()) {
			throw new Nano_Exception_UnsupportedConfigFormat(get_class($format));
		}

		$this->format = $format;
		$this->path   = $path;

		$this->load();
	}

	/**
	 * @return Nano_Routes
	 */
	public function routes() {
		return $this->routes;
	}

	/**
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	public function fileExists() {
		return
			file_exists($this->path . DIRECTORY_SEPARATOR . self::CONFIG_FILE_NAME)
			&& file_exists($this->path . DIRECTORY_SEPARATOR . self::ROUTES_FILE_NAME)
		;
	}

	/**
	 * @return boolean
	 * @param string $name
	 */
	public function exists($name) {
		if (!$this->fileExists()) {
			return false;
		}
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
		$this->config->$name = $value;
	}

	/**
	 * @return void
	 * @throws Nano_Exception
	 */
	protected function load() {
		if (!$this->fileExists()) {
			throw new Nano_Config_Exception('Configuration files not exists at ' . $this->path);
		}
		$this->config = $this->format->read($this->path . DIRECTORY_SEPARATOR . self::CONFIG_FILE_NAME);
		$this->routes = $this->format->readRoutes($this->path . DIRECTORY_SEPARATOR . self::ROUTES_FILE_NAME);
	}

}