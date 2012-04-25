<?php

class Nano_HelperBroker_Module {

	/**
	 * @var Application
	 */
	protected $application;

	/**
	 * @var string
	 */
	protected $module;

	/**
	 * @var Nano_Helper[]
	 */
	protected $helpers = array();

	/**
	 * @param Application $application
	 * @param string $module
	 */
	public function __construct(Application $application, $module) {
		$this->application = $application;
		$this->module      = $module;
	}

	/**
	 * @return Nano_Helper
	 * @param $name
	 */
	public function __get($name) {
		return $this->get($name);
	}

	/**
	 * @return Nano_Helper
	 * @param $name
	 * @param array $arguments
	 */
	public function __call($name, array $arguments) {
		return $this->get($name);
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
	 */
	protected function search($name) {
		$className = ucFirst($name) . 'Helper';
		$classPath = $this->application->modules->getPath(
			$this->module
			, Application::HELPERS_DIR_NAME . DIRECTORY_SEPARATOR . Nano_Loader::classToPath($className)
		);
		$fullClassName = Nano_Loader::formatModuleClassName($this->module, $className);

		if (!$this->application->loader->loadFileWithClass($fullClassName, $classPath)) {
			throw new Nano_Exception_HelperNotFound($name, $this->module);
		}

		return new $fullClassName;
	}

}