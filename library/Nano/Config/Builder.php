<?php

class Nano_Config_Builder {

	const PARENTS_FILE = '.parent.php';

	/**
	 * @var string
	 */
	protected $source = null, $destination = null;

	/**
	 * @var Nano_Config_Format[]|SplStack
	 */
	protected $formats;

	public function __construct() {
		$this->formats = new SplStack();
	}

	/**
	 * @return void
	 * @param string $path
	 */
	public static function createStub($path) {
		//todo: remove this method
		if (!file_exists($path)) {
			file_put_contents($path, '<?php return (object)array();');
		}
	}

	/**
	 * @return Nano_Config_Builder
	 * @param Nano_Config_Format $format
	 */
	public function addFormat(Nano_Config_Format $format) {
		$this->formats->push($format);
		return $this;
	}

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

	/**
	 * @return Nano_Config_Format
	 */
	public function detectFormat() {
		$this->formats->rewind();
		foreach ($this->formats as $format) {
			if ($format->available()) {
				return $format;
			}
		}
		return new Nano_Config_Format_Php();
	}

	/**
	 * @return void
	 * @param string $name
	 */
	public function build($name) {
		//todo: use configured format instead detect
		//todo: throw exception if format not available
		$this->detectFormat()->write($this->createSettings($name), $this->destination . DS . Nano_Config::CONFIG_FILE_NAME);
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
		return $this->source . DS . $name . DS . $file;
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
				$result[$key] = $value;
			}
		}
		return $result;
	}

}