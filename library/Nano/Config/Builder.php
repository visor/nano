<?php

class Nano_Config_Builder {

	const PARENTS_FILE = '.parent.php';

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

	/**
	 * @return void
	 * @param string $name
	 */
	public function build($name) {
		$settings = $this->createSettings($name);
		$source   = var_export($settings, true);
		$source   = str_replace('stdClass::__set_state(', 'Object::makeUsing(', $source);
		file_put_contents($this->destination, '<?php return (object)' . $source . ';');
	}

	protected function createSettings($name) {
		$parents  = $this->getParents($name);
		$settings = array();
		$i        = new DirectoryIterator($this->getFilePath($name, null));
		foreach ($parents as $parent) {
			$settings = array_merge($settings, $this->createSettings($parent));
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
				$settings[$section] = array_merge(
					$settings[$section]
					, $this->buildSingleFile($parents, $file->getPathName())
				);
			} else {
				$settings[$section] = $this->buildSingleFile($parents, $file->getPathName());
			}
		}
		return $settings;
	}

	/**
	 * @return string[]
	 */
	protected function getParents($name) {
		return $this->getFile($name, self::PARENTS_FILE, array());
	}

	/**
	 * @return array
	 * @param string[] $parents
	 * @param string $file
	 */
	protected function buildSingleFile($parents, $file) {
		$result = array();
		return include($file);
//		return $result;
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

}