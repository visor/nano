<?php

class Nano_HelperBroker {

	private static $helpers = array();

	/**
	 * @var Nano_HelperBroker
	 */
	private static $instance = null;

	/**
	 * @return Nano_HelperBroker
	 */
	public static function instance() {
		if (null == self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __call($method, array $arguments) {
		return call_user_func_array(
			  array($this->get($method), 'invoke')
			, $arguments
		);
	}

	/**
	 * @return Nano_Helper
	 * @param string $name
	 */
	public function get($name) {
		$key = strToLower($name);
		if (array_key_exists($key, self::$helpers)) {
			return self::$helpers[$key];
		}

		$helper = $this->search($name);
		if (null === $helper) {
			throw new RuntimeException('Helper ' . $helper . ' not found');;
		}

		self::$helpers[$key] = $helper;
		return self::$helpers[$key];
	}

	/**
	 * @return Nano_Helper
	 * @param string $name
	 */
	protected function search($name) {
		$className = $name . 'Helper';
		$fileName  = HELPERS . DS . $name . '.php';
		if (!class_exists($className)) {
			return null;
		}
		return new $className;
	}

}