<?php

namespace Nano\HelperBroker;

class Module {

	/**
	 * @var \Nano\Application
	 */
	protected $application;

	/**
	 * @var string
	 */
	protected $module;

	/**
	 * @var \Nano\Helper[]
	 */
	protected $helpers = array();

	/**
	 * @param \Nano\Application $application
	 * @param string $module
	 */
	public function __construct(\Nano\Application $application, $module) {
		$this->application = $application;
		$this->module      = $module;
	}

	/**
	 * @return \Nano\Helper
	 * @param $name
	 */
	public function __get($name) {
		return $this->get($name);
	}

	/**
	 * @return \Nano\Helper
	 * @param $name
	 * @param array $arguments
	 */
	public function __call($name, array $arguments) {
		return $this->get($name);
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
		$className = \Nano\Names::helperClass($name, $this->module);
		if (class_exists($className, false)) {
			return new $className;
		}

		$classPath = \Nano\Names::moduleFile($className);

		if (!$this->application->loader->loadFileWithClass($className, $classPath)) {
			throw new \Nano\Exception\HelperNotFound($name, $this->module);
		}

		return new $className;
	}

}