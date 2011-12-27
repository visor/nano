<?php

/**
 * @method ResourceHelper resource()
 */
class Nano_HelperBroker {

	/**
	 * @var Nano_Helper[]
	 */
	protected $helpers = array();

	/**
	 * @var Nano_HelperBroker_Module[]
	 */
	protected $modules = array();

	/**
	 * @var Nano_Dispatcher
	 */
	protected $dispatcher;

	/**
	 * @return Nano_HelperBroker_Module
	 * @param $module
	 *
	 * @thows Application_Exception_ModuleNotFound
	 */
	public function __get($module) {
		$moduleName = $this->getDispatcher()->application()->getModules()->nameToFolder($module . Nano_Modules::MODULE_SUFFIX);
		if (!$this->getDispatcher()->application()->getModules()->active($moduleName)) {
			throw new Application_Exception_ModuleNotFound($moduleName);
		}

		if (isSet($this->modules[$moduleName])) {
			return $this->modules[$moduleName];
		}

		$this->modules[$moduleName] = new Nano_HelperBroker_Module($this->getDispatcher()->application(), $moduleName);
		return $this->modules[$moduleName];
	}

	/**
	 * @return mixed
	 * @param $method
	 * @param array $arguments
	 */
	public function __call($method, array $arguments) {
		return $this->get($method, false)->invoke();
	}

	/**
	 * @return void
	 * @param Nano_Dispatcher $dispatcher
	 */
	public function setDispatcher(Nano_Dispatcher $dispatcher) {
		$this->dispatcher = $dispatcher;
	}

	/**
	 * @return Nano_Dispatcher
	 */
	public function getDispatcher() {
		if (null === $this->dispatcher) {
			$this->setDispatcher(Application::current()->getDispatcher());
		}
		return $this->dispatcher;
	}

	/**
	 * @return Nano_Helper
	 * @param string $name
	 * @param boolean $isClass
	 */
	protected function get($name, $isClass = false) {
		$key = strToLower($name);
		if (array_key_exists($key, $this->helpers)) {
			return $this->helpers[$key];
		}

		$helper = $this->search($isClass ? $name : $key, $isClass);
		$helper->setDispatcher($this->getDispatcher());
		$this->helpers[$key] = $helper;
		return $this->helpers[$key];
	}

	/**
	 * @return Nano_Helper
	 * @param string $name
	 * @param boolean $isClass
	 */
	protected function search($name, $isClass) {
		$className = $isClass ? $name : ucFirst($name) . 'Helper';
		if ($this->getDispatcher()->application()->loader()->loadClass($className)) {
			return new $className();
		}
		throw new Nano_Exception('Helper ' . $name . ' not found');
	}

}
