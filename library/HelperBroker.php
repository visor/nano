<?php

namespace Nano;

class HelperBroker {

	/**
	 * @var \Nano\Helper[]
	 */
	protected $helpers = array();

	/**
	 * @var \Nano\HelperBroker\Module[]
	 */
	protected $modules = array();

	/**
	 * @return \Nano\HelperBroker\Module
	 * @param string $module
	 *
	 * @throws \Nano\Application\Exception\ModuleNotFound
	 */
	public function __get($module) {
		$moduleName = \Nano::app()->modules->nameToFolder($module . \Nano\Application\Modules::MODULE_SUFFIX);
		if (!\Nano::app()->modules->active($moduleName)) {
			throw new \Nano\Application\Exception\ModuleNotFound($moduleName);
		}

		if (isSet($this->modules[$moduleName])) {
			return $this->modules[$moduleName];
		}

		$this->modules[$moduleName] = new \Nano\HelperBroker\Module(\Nano::app(), $moduleName);
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
	 * @return \Nano\Helper
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
	 * @return \Nano\Helper
	 * @param string $name
	 *
	 * @throws \Nano\Exception\HelperNotFound
	 */
	protected function search($name) {
		$className = \Nano\Names::helperClass($name);
		if (class_exists($className, false)) {
			return $className;
		}

		$classPath = \Nano\Names::applicationFile($className);
		if (!\Nano::app()->loader->loadFileWithClass($className, $classPath)) {
			throw new \Nano\Exception\HelperNotFound($name);
		}

		return new $className;
	}

}