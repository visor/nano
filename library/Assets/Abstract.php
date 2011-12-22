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
	 * @var int
	 */
	protected $time = 0;

	/**
	 * @var string
	 */
	protected $type = null;

	/**
	 * @var string
	 */
	protected $ext = null;

	/**
	 * @var string
	 */
	protected $output = null;

	public function __construct() {
		$this->time = 0;
	}

	/**
	 * @return string
	 * @param string $url
	 * @param array $params
	 */
	abstract protected function tag($url, array $params);

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

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
	 * @param boolean $php
	 * @param string $file
	 * @param array $params
	 */
	public function addItem($append, $php, $file, array $params = array()) {
		$key = $this->createKey($params);
		if (isset($this->items[$key]['files'][$file])) {
			return $this;
		}
		$time = fileMTime($file);
		if (!isSet($this->items[$key])) {
			$this->items[$key] = array(
				  'files'  => array($file => $php)
				, 'params' => $params
				, 'time'   => $time
			);
			return $this;
		}
		if ($append) {
			$this->items[$key]['files'][$file] = $php;
		} else {
			$this->items[$key]['files'] = array_merge(array($file => $php), $this->items[$key]['files']);
		}
		if ($time > $this->items[$key]['time']) {
			$this->items[$key]['time'] = $time;
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
	 * @return string
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
	 * @param array $headers
	 */
	public function display($group = self::DEFAULT_GROUP, &$headers = null) {
		$this->import();
		$time     = $this->items[$group]['time'];
		$modified = $this->isModified($group);
		if (is_array($headers)) {
			if ($modified) {
				$headers[] = 'X-Status: 200';
			} else {
				$headers[] = 'X-Status: 304';
			}
			$headers[] = 'Last-Modified: ' . gmDate('D, j M Y H:i:s', $time) .' GMT';
			$headers[] = 'Expires: ' . gmDate('D, j M Y H:i:s', $time + Date::ONE_MONTH) .' GMT';
			$headers[] = 'Cache-Control: max-age=' . Date::ONE_MONTH .', public';
		} else {
			if ($modified) {
				header('X-Status: 200', true, 200);
			} else {
				header('X-Status: 304', true, 304);
			}
			header('Expires: ' . gmDate('D, j M Y H:i:s', $time + Date::ONE_MONTH) .' GMT');
			header('Cache-Control: max-age=' . Date::ONE_MONTH .', public, must-revalidate, post-check=0, pre-check=0');
			header('Last-Modified: ' . gmDate('D, j M Y H:i:s', $time) .' GMT', true, $modified ? null : 304);
		}
		if ($modified) {
			return file_get_contents($this->getGroupFile($this->generateBaseName(), $group));
		}
		return null;
	}

	/**
	 * Removes all generated assets
	 *
	 * @return void
	 * @param array $ignore
	 * @param boolean $verbose
	 */
	public function clearCache(array $ignore = array(), $verbose = false, $path = null) {
		if (!in_array('.svn', $ignore)) {
			$ignore[] = '.svn';
		}
		if (null === $path) {
			$path = $this->getBasePath();
		}
		$i = new DirectoryIterator($path);
		foreach ($i as $item) {
			if ($item->isDot()) {
				continue;
			}
			if (in_array($item->getBaseName(), $ignore)) {
				continue;
			}
			if ($item->isDir()) {
				$this->clearCache(array(), $verbose, $item->getPathName());
				$result = @rmDir($item->getPathName());
			} else {
				$result = @unlink($item->getPathName());
			}
			if (true === $verbose) {
				echo $item->getPathName();
				if (false === $result) {
					echo ' ERROR';
				}
				echo PHP_EOL;
			}
		}
	}

	/**
	 * @return string
	 * @param string $base
	 */
	public function getBasePath($base = null) {
		$result = $this->output . DS . $this->type;
		if (null === $base) {
			return $result;
		}
		$result .= DS . $base;
		return $result;
	}

	/**
	 * @return boolean
	 * @param string $base
	 * @param string $group
	 */
	protected function shouldWrite($base, $group) {
		$time = $this->getGroupTime($base, $group);
		if ($this->items[$group]['time'] > $time) {
			return true;
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
		return $time;
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
	 * @param array $params
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
		return md5(serialize($this->items) . serialize($this->variables));
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
//		return Nano::helper()->resource()->cdn(
//			Nano::config('assets')->url . '/'. $this->type . '/' . $base . '/' . $group . '.' . $this->ext . '?' . $this->getGroupTime($base, $group)
//		);
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
		return $this->postProcessing($this->replaceVariables($result));
	}

	/**
	 * @return string
	 * @param string $string
	 */
	protected function replaceVariables($string) {
		$result   = $string;
		$search   = preg_quote('<?=$cdn?>');
		$callback = function() { return Nano::helper()->resource()->cdn(''); };
		$result = preg_replace_callback('/' . $search .'/i', $callback, $result);
		foreach ($this->variables as $name => $value) {
			$result = str_replace('<?=$' . $name . '?>', $value, $result);
		}
		return $result;
	}

	/**
	 * @return boolean
	 * @param string $group
	 */
	protected function isModified($group) {
		if (!isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
			return true;
		}
		if (strToTime($_SERVER['HTTP_IF_MODIFIED_SINCE']) < $this->items[$group]['time']) {
			return true;
		}
		return false;
	}

}