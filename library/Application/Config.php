<?php

namespace Nano\Application;

class Config {

	const CONFIG_FILE_NAME  = 'configuration';
	const ROUTES_FILE_NAME  = 'routes';
	const CONFIG_NAME       = '__name';

	/**
	 * @var Config\Format
	 */
	protected $format = null;

	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @var array
	 */
	protected $config = null;

	/**
	 * @var \Nano\Routes
	 */
	protected $routes = null;

	/**
	 * @return Config\Format
	 * @param string $name
	 *
	 * @throws \Nano\Exception\UnsupportedConfigFormat
	 */
	public static function format($name) {
		/** @var Config\Format $result */
		$className = __CLASS__ . '\Format\\' . ucFirst(strToLower($name));

		if (false === class_exists($className)) {
			throw new \Nano\Exception\UnsupportedConfigFormat($name);
		}

		$class = new \ReflectionClass($className);
		if (!$class->implementsInterface('\Nano\Application\Config\Format') || !$class->isInstantiable()) {
			throw new \Nano\Exception\UnsupportedConfigFormat($name);
		}

		$result = $class->newInstance();
		if (!$result->available()) {
			throw new \Nano\Exception\UnsupportedConfigFormat($name);
		}

		return $result;
	}

	/**
	 * @param string $path
	 * @param Config\Format $format
	 *
	 * @throws \Nano\Exception\UnsupportedConfigFormat
	 */
	public function __construct($path, Config\Format $format) {
		if (false === $format->available()) {
			throw new \Nano\Exception\UnsupportedConfigFormat(get_class($format));
		}

		$this->format = $format;
		$this->path   = $path;
	}

	/**
	 * @return \Nano\Routes
	 */
	public function routes() {
		$this->load();
		return $this->routes;
	}

	/**
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * @return boolean
	 */
	public function configurationExists() {
		return file_exists($this->path . DS . self::CONFIG_FILE_NAME);
	}

	/**
	 * @return boolean
	 */
	public function routesExists() {
		return file_exists($this->path . DS . self::ROUTES_FILE_NAME);
	}

	/**
	 * @return string
	 */
	public function name() {
		$this->load();
		if (false === $this->exists(self::CONFIG_NAME)) {
			return null;
		}
		return $this->config->{self::CONFIG_NAME};
	}

	/**
	 * @return boolean
	 * @param string $name
	 */
	public function exists($name) {
		if (!$this->configurationExists()) {
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
	 * @throws Config\Exception
	 */
	protected function load() {
		if (null !== $this->config) {
			return;
		}
		if (!$this->configurationExists() && !$this->routesExists()) {
			throw new Config\Exception('Configuration files not exists at ' . $this->path);
		}

		$this->config = $this->format->read($this->path . DS . self::CONFIG_FILE_NAME);
		if (empty($this->config)) {
			$this->config = new \stdClass;
		}
		if ($this->routesExists()) {
			$this->routes = $this->format->readRoutes($this->path . DS . self::ROUTES_FILE_NAME);
		} else {
			$this->routes = new \Nano\Routes();
		}
	}

}