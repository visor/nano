<?php

class Nano_Config {

	const CONFIG_FILE_NAME  = 'configuration';
	const ROUTES_FILE_NAME  = 'routes';
	const CHANGED_FILE_NAME = 'changed';

	/**
	 * @var Nano_Config_Format
	 */
	private static $format = null;

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
	 * @param string $path
	 */
	public function __construct($path) {
		$this->setPath($path);
	}

	/**
	 * @return Nano_Config_Format
	 * @param string $name
	 */
	public static function formatFactory($name) {
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
	 * @return void
	 * @param Nano_Config_Format $value
	 */
	public static function setFormat(Nano_Config_Format $value) {
		self::$format = $value;
	}

	/**
	 * @return Nano_Config_Format
	 */
	public static function getFormat() {
		if (null === self::$format) {
			throw new Nano_Config_Exception('No configuration format specified');
		}
		if (false === self::$format->available()) {
			throw new Nano_Config_Exception('Specified configuration format not available');
		}
		return self::$format;
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

	/**
	 * @return void
	 * @param string $value
	 */
	public function setPath($value) {
		$this->path   = $value;
		$this->config = null;
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

	/**
	 * @return void
	 * @throws Nano_Exception
	 */
	protected function load() {
		if (null !== $this->config) {
			return;
		}
		if (!$this->fileExists()) {
			throw new Nano_Config_Exception('Configuration files not exists at ' . $this->path);
		}
		$this->config = self::getFormat()->read($this->path . DIRECTORY_SEPARATOR . self::CONFIG_FILE_NAME);
		$this->routes = self::getFormat()->readRoutes($this->path . DIRECTORY_SEPARATOR . self::ROUTES_FILE_NAME);
	}

}