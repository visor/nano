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
	 * @param array $item
	 * @param string $group
	 */
	abstract protected function tag($url, array $item, $group);

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
		if (isset($this->items[$key][$file])) {
			return $this;
		}
		$item = array(
			  'params' => $params
			, 'php'    => $php
		);
		if (!isset($this->items[$key])) {
			$this->items[$key] = array($file => $item);
			return $this;
		}
		if ($append) {
			$this->items[$key][$file] = $item;
		} else {
			$this->items[$key] = array_merge(array($file => $item), $this->items[$key]);
		}
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
			$tags[] = $this->tag($this->getGroupUrl($base, $group), $item, $group);
			$this->write($base, $group);
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
		return $this->getGroupContents($group);
	}

	/**
	 * @return void
	 * @param string $base
	 * @param string $group
	 */
	protected function write($base, $group) {
		$file     = $this->getGroupFile($base, $group);
		$contents = $this->getGroupContents($group);
		file_put_contents($file, $contents);
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
		return Nano::config('assets')->url . '/'. $this->type . '/' . $base . '/' . $group . '.' . $this->ext;
	}

	/**
	 * @return string
	 * @param string $group
	 */
	protected function getGroupContents($group) {
		$result = '';
		foreach ($this->items[$group] as $file => $item) {
			if (true === $item['php']) {
				ob_start();
				include($file);
				$result .= ob_get_clean();
			} else {
				$result .= file_get_contents($file);
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