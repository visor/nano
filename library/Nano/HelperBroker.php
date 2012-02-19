<?php

/**
 * @method ResourceHelper resource()
 */
class Nano_HelperBroker {

	/**
	 * @var Application
	 */
	protected $application;

	/**
	 * @var Nano_Helper[]
	 */
	protected $helpers = array();

	/**
	 * @var Nano_HelperBroker_Module[]
	 */
	protected $modules = array();

	public function __construct(Application $application) {
		$this->application = $application;
	}

	/**
	 * @return Nano_HelperBroker_Module
	 * @param $module
	 *
	 * @thows Application_Exception_ModuleNotFound
	 */
	public function __get($module) {
		$moduleName = $this->application->modules->nameToFolder($module . Nano_Modules::MODULE_SUFFIX);
		if (!$this->application->modules->active($moduleName)) {
			throw new Application_Exception_ModuleNotFound($moduleName);
		}

		if (isSet($this->modules[$moduleName])) {
			return $this->modules[$moduleName];
		}

		$this->modules[$moduleName] = new Nano_HelperBroker_Module($this->application, $moduleName);
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
		$helper->setDispatcher($this->application->dispatcher);
		$this->helpers[$key] = $helper;

		return $this->helpers[$key];
	}

	/**
	 * @return Nano_Helper
	 * @param string $name
	 */
	protected function search($name) {
		$className = ucFirst($name) . 'Helper';

		if (!class_exists($className, false)) {
			$classPath = $this->application->rootDir . DIRECTORY_SEPARATOR . Application::HELPERS_DIR_NAME . DIRECTORY_SEPARATOR . Nano_Loader::classToPath($className);

			if (!$this->application->loader->loadFileWithClass($className, $classPath)) {
				throw new Nano_Exception_HelperNotFound($name);
			}
		}

		return new $className;
	}

}