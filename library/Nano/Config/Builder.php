<?php

class Nano_Config_Builder {

	const PARENTS_FILE = '.parent.php';
	const ROUTES_FILE  = 'routes.php';

	/**
	 * @var string
	 */
	protected $source = null, $destination = null;

	/**
	 * @return Nano_Config_Builder
	 * @param string $value
	 */
	public function setSource($value) {
		$this->source = $value;
		return $this;
	}

	/**
	 * @return Nano_Config_Builder
	 * @param string $value
	 */
	public function setDestination($value) {
		$this->destination = $value;
		return $this;
	}

	public function clean() {
		if (null === $this->destination) {
			return;
		}
		if (file_exists($this->destination . DIRECTORY_SEPARATOR . Nano_Config::CONFIG_FILE_NAME)) {
			unLink($this->destination . DIRECTORY_SEPARATOR . Nano_Config::CONFIG_FILE_NAME);
		}
		if (file_exists($this->destination . DIRECTORY_SEPARATOR . Nano_Config::ROUTES_FILE_NAME)) {
			unLink($this->destination . DIRECTORY_SEPARATOR . Nano_Config::ROUTES_FILE_NAME);
		}
	}

	/**
	 * @return void
	 * @param string $name
	 */
	public function build($name) {
		$this->buildConfiguration($name);
		$this->buildRoutes($name);
	}

	/**
	 * @return void
	 * @param string $name
	 */
	protected function buildConfiguration($name) {
		Nano_Config::getFormat()->write(
			$this->createSettings($name)
			, $this->destination . DIRECTORY_SEPARATOR . Nano_Config::CONFIG_FILE_NAME
		);
	}

	/**
	 * @return void
	 * @param string $name
	 */
	protected function buildRoutes($name) {
		$routes    = new Nano_Routes();
		$parents   = $this->getParents($name);
		$parents[] = $name;
		foreach ($parents as $name) {
			$routesFile = $this->getFilePath($name, self::ROUTES_FILE);
			if (file_exists($routesFile)) {
				include($this->getFilePath($name, self::ROUTES_FILE));
			}
		}

		Nano_Config::getFormat()->writeRoutes(
			$routes
			, $this->destination . DIRECTORY_SEPARATOR . Nano_Config::ROUTES_FILE_NAME
		);
	}

	protected function createSettings($name) {
		$parents  = $this->getParents($name);
		$settings = array();
		$i        = new DirectoryIterator($this->getFilePath($name, null));
		foreach ($parents as $parent) {
			$settings = $this->mergeSections($settings, $this->createSettings($parent));
		}
		foreach ($i as /** @var DirectoryIterator $file */$file) {
			if ($file->isDir() || $file->isDir() || !$file->isReadable()) {
				continue;
			}
			if (self::PARENTS_FILE == $file->getBaseName()) {
				continue;
			}
			if (self::ROUTES_FILE == $file->getBaseName()) {
				continue;
			}
			if ('php' !== pathInfo($file->getBaseName(), PATHINFO_EXTENSION)) {
				continue;
			}
			$section = $file->getBaseName('.php');
			if (isSet($settings[$section])) {
				$settings[$section] = $this->mergeSections(
					$settings[$section]
					, $this->buildSingleFile($file->getPathName())
				);
			} else {
				$settings[$section] = $this->buildSingleFile($file->getPathName());
			}
		}
		return $settings;
	}

	/**
	 * @return string[]
	 * @param string $name
	 */
	protected function getParents($name) {
		return $this->getFile($name, self::PARENTS_FILE, array());
	}

	/**
	 * @return array
	 * @param string $file
	 */
	protected function buildSingleFile($file) {
		return include($file);
	}

	/**
	 * @return mixed
	 * @param string $name
	 * @param string $file
	 * @param mixed $default
	 */
	protected function getFile($name, $file, $default = null) {
		$path = $this->getFilePath($name, $file);
		if (file_exists($path)) {
			return include($path);
		}
		return $default;
	}

	/**
	 * @return string
	 * @param string $name
	 * @param string $file
	 */
	protected function getFilePath($name, $file) {
		return $this->source . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . $file;
	}

	/**
	 * @return array
	 * @param array $one
	 * @param array $two
	 */
	protected function mergeSections($one, $two) {
		$result = $one;
		foreach ($two as $key => $value) {
			if (is_array($value) && isSet($result[$key]) && is_array($result[$key])) {
				$result[$key] = $this->mergeSections($result[$key], $value);
			} else {
				//todo: check if key is numeric and use $result[] instead $result[$key]
				$result[$key] = $value;
			}
		}
		return $result;
	}

}