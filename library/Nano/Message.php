<?php

class Nano_Message {

	/**
	 * @var Nano_Message
	 */
	private static $instance = null;

	/**
	 * @var string
	 */
	protected $lang = null;

	/**
	 * @var string[]
	 */
	protected $strings = array();

	/**
	 * @var Nano_Message_Plural_Interface
	 */
	protected $plural = null;

	/**
	 * @return Nano_Message
	 * @param boolean $reset
	 */
	public static function instance($reset = false) {
		if (null == self::$instance || true === $reset) {
			self::$instance = new self();
			if (isset(Nano::config('web')->lang)) {
				self::$instance->lang(Nano::config('web')->lang);
			}
		}
		return self::$instance;
	}

	/**
	 * @return string
	 * @param srting $id
	 */
	public function lang($id = null) {
		if (null !== $id) {
			$this->lang   = strToUpper($id);
			$this->plural = null;
		}
		return $this->lang;
	}

	/**
	 * @return Nano_Message
	 * @param string $file
	 */
	public function load($file) {
		$path = Application::current()->getRootDir() . DS . 'messages' . DS . $this->fileName($file);
		if (!is_file($path)) {
			throw new Exception('File "' . $path . '" not found');
		}

		$new = include($path);
		if (!is_array($new)) {
			throw new Exception('No strings loaded from file "' . $file . '"');
		}
		$this->strings = array_merge($this->strings, $new);
	}

	/**
	 * @return string
	 * @param string $id
	 */
	public function m($id) {
		if (isset($this->strings[$id])) {
			return $this->strings[$id];
		}
		return null;
	}

	/**
	 * @return string
	 * @param string $id
	 */
	public function f($id) {
		$args = func_get_args();
		array_shift($args);
		return $this->fa($id, $args);
	}

	/**
	 * @return string
	 * @param string $id
	 * @param array $args
	 */
	public function fa($id, array $args) {
		$message = $this->m($id);
		if (null === $message) {
			return null;
		}
		return vsprintf($message, $args);
	}

	/**
	 * @return stirng
	 * @param int $number
	 * @param string $id
	 */
	public function p($number, $id) {
		$message = $this->m($id);
		if (null === $message) {
			return null;
		}
		return $this->plural($number, $message);
	}

	/**
	 * @return string
	 * @param int $number
	 * @param array $message
	 */
	public function plural($number, array $message) {
		if (!$this->lang) {
			return null;
		}
		if (null === $this->plural) {
			$className = 'Nano_Message_Plural_' . $this->lang;
			if (!class_exists($className)) {
				return null;
			}
			$this->plural = new $className;
		}
		return $this->plural->get($number, $message);
	}

	/**
	 * @return string
	 * @param string $base
	 */
	protected function fileName($base) {
		return $base . '.php';
	}

}