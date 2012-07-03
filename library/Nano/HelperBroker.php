<?php

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
	 * @return Nano_HelperBroker_Module
	 * @param string $module
	 *
	 * @throws Application_Exception_ModuleNotFound
	 */
	public function __get($module) {
		$moduleName = Nano::app()->modules->nameToFolder($module . Nano_Modules::MODULE_SUFFIX);
		if (!Nano::app()->modules->active($moduleName)) {
			throw new Application_Exception_ModuleNotFound($moduleName);
		}

		if (isSet($this->modules[$moduleName])) {
			return $this->modules[$moduleName];
		}

		$this->modules[$moduleName] = new Nano_HelperBroker_Module(Nano::app(), $moduleName);
		return $this->modules[$moduleName];
	}

	/**
	 * @return mixed
	 * @param $method
	 * @param array $arguments
	 */
	public function __call($method, array $arguments) {
		return $this->get($method);
	}

	/**
	 * @return Nano_Helper
	 * @param string $name
	 */
	protected function get($name) {
		$key = strToLower($name);
		if (array_key_exists($key, $this->helpers)) {
			return $this->helpers[$key];
		}

		$helper = $this->search($key);
		$this->helpers[$key] = $helper;

		return $this->helpers[$key];
	}

	/**
	 * @return Nano_Helper
	 * @param string $name
	 *
	 * @throws Nano_Exception_HelperNotFound
	 */
	protected function search($name) {
		$className = ucFirst($name) . 'Helper';

		if (!class_exists($className, false)) {
			$classPath = Nano::app()->rootDir . DIRECTORY_SEPARATOR . Application::HELPERS_DIR_NAME . DIRECTORY_SEPARATOR . \Nano\Loader::classToPath($className);

			if (!Nano::app()->loader->loadFileWithClass($className, $classPath)) {
				throw new Nano_Exception_HelperNotFound($name);
			}
		}

		return new $className;
	}

}