<?php

/**
 * @method ResourceHelper resource()
 */
class Nano_HelperBroker {

	/**
	 * @var Nano_Helper[string]
	 */
	private static $helpers = array();

	/**
	 * @var Nano_HelperBroker
	 */
	private static $instance = null;

	/**
	 * @var Nano_Dispatcher
	 */
	private $dispatcher;

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
	 * @param boolean $isClass
	 */
	public function get($name, $isClass = false) {
		$key = strToLower($name);
		if (array_key_exists($key, self::$helpers)) {
			self::$helpers[$key]->setDispatcher($this->getDispatcher());
			return self::$helpers[$key];
		}

		$helper = $this->search($isClass ? $name : $key, $isClass);
		if (null === $helper) {
			throw new RuntimeException('Helper ' . $name . ' not found');
		}

		$helper->setDispatcher($this->getDispatcher());
		self::$helpers[$key] = $helper;
		return self::$helpers[$key];
	}

	/**
	 * @return void
	 * @param Nano_Dispatcher $dispatcher
	 */
	public function setDispatcher(Nano_Dispatcher $dispatcher = null) {
		$this->dispatcher = $dispatcher;
	}

	/**
	 * @return Nano_Dispatcher
	 */
	public function getDispatcher() {
		if (null === $this->dispatcher) {
			$this->setDispatcher(Nano::dispatcher());
		}
		return $this->dispatcher;
	}

	/**
	 * @return Nano_Helper
	 * @param string $name
	 * @param boolean $isClass
	 */
	protected function search($name, $isClass) {
		if ($isClass) {
			$className = $name;
		} else {
			$className = ucFirst($name) . 'Helper';
		}
		if (!class_exists($className)) {
			return null;
		}
		return new $className();
	}

}