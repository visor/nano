<?php

abstract class Assets_Abstract {

	const DEFAULT_NAME  = 'default';
	const DEFAULT_GROUP = 'default';

	/**
	 * @var array[string]
	 */
	protected $data = array();

	/**
	 * @var mixed[string]
	 */
	protected $variables = array();

	/**
	 * @var int
	 */
	protected $time;

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
	 * @param string $name
	 */
	abstract public function tag($name = self::DEFAULT_NAME);

	/**
	 * @param string $file
	 */
	public function php($file, $group = self::DEFAULT_GROUP) {
		$this->addData(true, $group, $file, null, true);
		return $this;
	}

	/**
	 * @return boolean
	 * @param string $name
	 */
	public function exists($name = self::DEFAULT_NAME) {
		return file_exists($this->getBaseDirectory($name));
	}

	/**
	 * @return void
	 * @param string $name
	 */
	public function load($name = self::DEFAULT_NAME) {
		$meta = $this->getMeta($name);
		$this->data      = $meta['files'];
		$this->variables = $meta['variables'];
		$this->time      = $meta['time'];
	}

	/**
	 * @return void
	 * @param string $name
	 */
	public function build($name = self::DEFAULT_NAME) {
		$dir = $this->getBaseDirectory($name);
		if (!file_exists($dir)) {
			mkDir($dir);
		}
		foreach ($this->data as $groupName => $files) {
			$contents = $this->buildGroup($groupName, $files);
			$contents = $this->replaceVariables($contents);
			$contents = $this->postProcessing($contents);
			$this->write($name, $groupName, $contents);
		}
		$this->writeMeta($name);
	}

	/**
	 * @return void
	 * @param string $name
	 * @param string $group
	 */
	public function import($name = self::DEFAULT_NAME, $group = self::DEFAULT_GROUP) {
		$this->load($name);
		if ($this->isModified()) {
			header('Last-Modified: ' . gmDate('D, j M Y H:i:s', $this->time) .' GMT');
			header('Expires: ' . gmDate('D, j M Y H:i:s', $this->time + Date::ONE_MONTH) .' GMT');
			header('Cache-Control: max-age=' . Date::ONE_MONTH .', public');
			include $this->getCacheFileName($name, $group);
		} else {
			header('Last-Modified: ' . gmDate('D, j M Y H:i:s', $this->time) .' GMT', true, 304);
			header('Expires: ' . gmDate('D, j M Y H:i:s', $this->time + Date::ONE_MONTH) .' GMT');
			header('Cache-Control: max-age=' . Date::ONE_MONTH .', public');
		}
	}

	/**
	 * Return collected data
	 *
	 * @return array
	 */
	public function getData() {
		return $this->data;
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
	 * @return Assets_Abstract
	 * @param int $value
	 */
	public function setTime($value) {
		$this->time = $value;
		return $this;
	}

	protected function addData($append, $group, $file, $params = null, $script = false) {
		$item = array(
			  'file'   => $file
			, 'params' => $params
			, 'script' => $script
		);
		if (!isset($this->data[$group])) {
			$this->data[$group] = array($item);
			return;
		}
		if ($append) {
			$this->data[$group][] = $item;
		} else {
			array_unshift($this->data[$group], $item);
		}
	}

	/**
	 * Generates group name using passed parameters
	 *
	 * @return string
	 * @param  string $param1[, string $param2[, ...]]
	 */
	protected function getGroup() {
		$params = func_get_args();
		return implode('-', $params);
	}

	/**
	 * @return string
	 * @param string $name
	 */
	protected function getBaseDirectory($name) {
		return Nano::config('assets')->path . DS . $this->type . DS . $name;
	}

	/**
	 * @return DirectoryIterator
	 * @param string $name
	 */
	protected function getFiles($name) {
	}

	/**
	 * @return array
	 * @param string $name
	 */
	protected function getMeta($name) {
		$source    = $this->getMetaFileName($name);
		$files     = array();
		$variables = array();
		$time      = array();
		if (file_exists($source)) {
			include($source);
		}
		return array(
			  'files'     => $files
			, 'variables' => $variables
			, 'time'      => $time
		);
	}

	/**
	 * @return string
	 * @param string $groupName
	 * @param array $files
	 */
	protected function buildGroup($groupName, array $files) {
		$result = '';
		foreach ($files as $info) {
			if (true === $info['script']) {
				ob_start();
				include($info['file']);
				$result .= ob_get_clean();
				ob_end_clean();
			} else {
				$result .= file_get_contents($info['file']);
			}
		}
		return $result;
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

	/**
	 * @return string
	 * @param string $string
	 */
	protected function postProcessing($string) {
		return $string;
	}

	/**
	 * @param string $name
	 * @param string $group
	 * @param string $string
	 */
	protected function write($name, $group, $string) {
		$dir = $this->getBaseDirectory($name);
		file_put_contents($this->getCacheFileName($name, $group), $string);
	}

	/**
	 * @return string
	 * @param string $name
	 * @param string $group
	 */
	protected function getMetaFileName($name) {
		return $this->getBaseDirectory($name) . DIRECTORY_SEPARATOR . 'meta.php';
	}

	/**
	 * @return string
	 * @param string $name
	 * @param string $group
	 */
	protected function getCacheFileName($name, $group) {
		return $this->getBaseDirectory($name) . DIRECTORY_SEPARATOR . $group . '.' . $this->ext;
	}

	protected function writeMeta($name) {
		$contents = '<?php' . PHP_EOL
			. PHP_EOL . '$files = ' . var_export($this->data, true) . ';'
			. PHP_EOL . '$variables = ' . var_export($this->variables, true) . ';'
			. PHP_EOL . '$time = ' . date('U', $this->time ? $this->time : time()) . ';';
		;
		file_put_contents($this->getMetaFileName($name), $contents);
	}

	/**
	 * @return boolean
	 */
	protected function isModified() {
		if (!isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
			return true;
		}
		if (strToTime($_SERVER['HTTP_IF_MODIFIED_SINCE']) > $this->time) {
			return true;
		}
		return false;
	}

}