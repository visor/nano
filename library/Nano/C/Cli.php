<?php

abstract class Nano_C_Cli extends Nano_C {

	const DEFAULT_ACTION = 'index';

	/**
	 * @var string[]
	 */
	protected $args = null;

	/**
	 * @return void
	 * @param string[] $args
	 */
	public static function main($controller, $action, array $args) {
		if (null === $action) {
			$action = self::DEFAULT_ACTION;
		}
		$className = Nano_Dispatcher::formatName($controller, true);
		try {
			$class = new ReflectionClass($className);
		} catch (ReflectionException $e) {
			self::error('Unknown controller: ' . $controller);
			return;
		}
		if (!$class->isSubclassOf(__CLASS__)) {
			self::error('Not CLI controller: ' . $controller);
			return;
		}
		$method = Nano_Dispatcher::formatName($action, false);
		if (!$class->hasMethod($method)) {
			self::error('Unknown action: ' . $action);
			return;
		}

		$controller = $class->newInstance();
		/**
		 * @var Nano_C_Cli $controller
		 */
		$controller->args = $args;
		$controller->run($action);
	}

	/**
	 * @return array
	 * @param  string $string
	 */
	public static function extractControllerAction($string) {
		$result = explode('.', strToLower($string));
		if (1 == count($result)) {
			$result[] = self::DEFAULT_ACTION;
		}
		return $result;
	}

	/**
	 * @return void
	 */
	public static function usage() {
		echo baseName($_SERVER['argv'][0]) . ' controller.action [options]', PHP_EOL;
	}

	public function __construct() {}

	/**
	 * @return void
	 * @param string $action
	 */
	public function run($action) {
		$method = Nano_Dispatcher::formatName($action, false);
		$this->init();
		try {
			$this->$method();
		} catch (Exception $e) {
			throw $e;
		}
	}

	/**
	 * @return void
	 * @param  $message
	 */
	protected static function error($message) {
		echo $message, PHP_EOL;
	}

	/**
	 * @return void
	 */
	protected static function help() {
	}

}