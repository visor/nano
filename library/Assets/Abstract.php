<?php

abstract class Assets_Abstract {

	const DEFAULT_GROUP = 'default';

	/**
	 * @var array[string]
	 */
	protected $items = array();

	/**
	 * @var mixed[string]
	 */
	protected $variables = array();

	/**
	 * @var string
	 */
	protected $output = null;

	/**
	 * @var string
	 */
	protected $type = null;

	/**
	 * @var string
	 */
	protected $ext = null;

	/**
	 * @return string
	 * @param string $url
	 * @param array $params
	 */
	abstract protected function tag($url, array $params);

	/**
	 * Sets output folder for compiled assets
	 *
	 * @return Assets_Abstract
	 * @param string $path
	 */
	public function setOutput($path) {
		$this->output = $path;
		return $this;
	}

	/**
	 * Return collected data
	 *
	 * @return array
	 */
	public function getItems() {
		return $this->items;
	}

	/**
	 * @return Assets_Abstract
	 * @param boolean $append
	 * @param boolean $script
	 * @param string $file
	 * @param string $group
	 * @param array $params
	 */
	public function addItem($append, $php, $file, array $params = array()) {
		$key = $this->createKey($params);
		if (isset($this->items[$key]['files'][$file])) {
			return $this;
		}
		if (!isset($this->items[$key])) {
			$this->items[$key] = array(
				  'files'  => array($file => $php)
				, 'params' => $params
			);
			return $this;
		}
		if ($append) {
			$this->items[$key]['files'][$file] = $php;
		} else {
			$this->items[$key]['files'] = array_merge(array($file => $php), $this->items[$key]['files']);
		}
		return $this;
	}

	/**
	 * @return Assets_Abstract
	 */
	public function clean() {
		$this->items = array();
		return $this;
	}


	/**
	 * @return Assets_Abstract
	 * @param string|array $name
	 * @param mixed $value
	 */
	public function variable($name, $value = null) {
		if (null == $value && is_array($name)) {
			foreach ($name as $variableName => $variableValue) {
				$this->variables[$variableName] = (string)$variableValue;
			}
		} else {
			$this->variables[$name] = (string)$value;
		}
		return $this;
	}

	/**
	 * @return void
	 * @param string $name
	 * @param string $group
	 */
	public function import() {
		if (null === $this->output) {
			throw new RuntimeException('No output folder');
		}
		if (!is_dir($this->output)) {
			throw new RuntimeException('No output folder');
		}

		$base   = $this->generateBaseName();
		$folder = $this->getBasePath($base);
		if (!file_exists($folder)) {
			mkDir($folder, 0777, true);
		}
		$tags = array();
		foreach ($this->items as $group => $item) {
			if ($this->shouldWrite($base, $group)) {
				$this->write($base, $group);
			}
			$tags[] = $this->tag($this->getGroupUrl($base, $group), $item['params'], $group);
		}
		return implode(PHP_EOL, $tags);
	}

	/**
	 * Returns contents of given group
	 *
	 * @return string
	 * @param string $group
	 */
	public function get($group = self::DEFAULT_GROUP) {
		$this->import();
		return file_get_contents($this->getGroupFile($this->generateBaseName(), $group));
	}

	/**
	 * @return boolean
	 * @param string $base
	 * @param string $group
	 */
	protected function shouldWrite($base, $group) {
		$time = $this->getGroupTime($base, $group);
		foreach ($this->items[$group]['files'] as $file => $php) {
			if (fileMTime($file) > $time) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @return void
	 * @param string $base
	 * @param string $group
	 */
	protected function write($base, $group) {
		$time     = null;
		$file     = $this->getGroupFile($base, $group);
		$contents = $this->getGroupContents($group, $time);
		file_put_contents($file, $contents);
		touch($file, $time);
	}

	/**
	 * @return string
	 * @param string $string
	 */
	protected function postProcessing($string) {
		return $string;
	}

	/**
	 * Builds string key for given group and params array
	 *
	 * @return string
	 * @param string $group
	 * @param string[string] $params
	 */
	protected function createKey(array $params) {
		$result = array_values($params);
		if (empty($result)) {
			$result = array(self::DEFAULT_GROUP);
		}
		$result = implode('-', $result);
		$result = strToLower($result);
		$result = str_replace(' ', '', $result);

		return $result;
	}

	/**
	 * @return string
	 */
	protected function generateBaseName() {
		return md5(serialize($this->items));
	}

	/**
	 * @return string
	 * @param string $base
	 */
	protected function getBasePath($base) {
		return $this->output . DS . $this->type . DS . $base;
	}

	/**
	 * @return string
	 */
	protected function getGroupFile($base, $group) {
		return $this->output . DS . $this->type . DS . $base . DS . $group . '.' . $this->ext;
	}

	/**
	 * @return string
	 * @param string $base
	 * @param string $group
	 */
	protected function getGroupUrl($base, $group) {
		return Nano::config('assets')->url . '/'. $this->type . '/' . $base . '/' . $group . '.' . $this->ext . '?' . $this->getGroupTime($base, $group);
	}

	/**
	 * @return int
	 * @param string $group
	 */
	protected function getGroupTime($base, $group) {
		$file = $this->getGroupFile($base, $group);
		if (file_exists($file)) {
			return fileMTime($file);
		}
		return 0;
	}

	/**
	 * @return string
	 * @param string $group
	 * @param int $time
	 */
	protected function getGroupContents($group, &$time) {
		$result = '';
		$time   = 0;
		foreach ($this->items[$group]['files'] as $file => $php) {
			if (true === $php) {
				ob_start();
				include($file);
				$result .= ob_get_clean();
			} else {
				$result .= file_get_contents($file);
			}
			$fileTime = fileMTime($file);
			if ($time < $fileTime) {
				$time = $fileTime;
			}
		}
		return $this->replaceVariables($result);
	}

	/**
	 * @return string
	 * @param string $string
	 */
	protected function replaceVariables($string) {
		$result = $string;
		foreach ($this->variables as $name => $value) {
			$result = str_replace('<?=$' . $name . '?>', $value, $result);
		}
		return $result;
	}

}