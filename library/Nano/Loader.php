<?php

class Nano_Loader {

	const NAME_SEPARATOR        = '_';
	const MODULE_NAME_SEPARATOR = '-';
	const MODULE_PREFIX         = 'M_';

	private static $modules = array();

	private static $types = array(
		  'Library'    => 'library'
		, 'Controller' => 'controllers'
		, 'Model'      => 'models'
		, 'Plugin'     => 'plugins'
	);

	/**
	 * @return boolean
	 * @param string $className
	 */
	public static function load($className) {
		try {
			if (self::isModuleClass($className)) {
				return self::loadModuleClass($className);
			}
			if (false === include(self::classToPath($className))) {
				return false;
			}
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	public static function loadModuleClass($className) {
		try {
			list($module, $type, $name) = self::extractModuleClassParts($className);
			if (!Nano::modules()->active($module)) {
				return false;
			}
			$filePath = Nano::modules()->getPath($module, self::typeToFolder($type) . DS . self::classToPath($name));
			if (false === include($filePath)) {
				return false;
			}
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	public static function initLibraries(Nano_Modules $modules) {
		set_include_path(
			LIB
			. PS . APP_LIB
			. PS . MODELS
			. PS . CONTROLLERS
			. PS . PLUGINS
			. PS . HELPERS
			. PS . get_include_path()
		);
	}

	public static function initModuleLibraries(Nano_Modules $modules, $name) {
//		$moduleRoot = $modules->getPath($name);
//		set_include_path(
//			$moduleRoot . DS . 'library'
//			. PS . $moduleRoot . DS . 'models'
//			. PS . $moduleRoot . DS . 'controllers'
//			. PS . $moduleRoot . DS . 'plugins'
//			. PS . $moduleRoot . DS . 'helpers'
//			. PS . get_include_path()
//		);
	}

	/**
	 * @return string
	 * @param string $name
	 */
	public static function classToPath($name) {
		return str_replace(self::NAME_SEPARATOR, DS, $name) . '.php';
	}

	/**
	 * @return boolean
	 * @param string $className
	 */
	public static function isModuleClass($className) {
		if (0 !== strPos($className, self::MODULE_PREFIX)) {
			return false;
		}
		if (0 === preg_match('/^' . self::MODULE_PREFIX . '[a-zA-Z]+_.+/', $className)) {
			return false;
		}
		return true;
	}

	/**
	 * @return string
	 * @param string $className
	 */
	public static function extractModuleClassParts($className) {
		$start     = strLen(self::MODULE_PREFIX);
		$typeStart = strPos($className, self::NAME_SEPARATOR, $start);
		$nameStart = strPos($className, self::NAME_SEPARATOR, $typeStart + 1);
		$module    = subStr($className, $start, $typeStart - 2);
		$module    = self::moduleToFolderName($module);
		$type      = subStr($className, $typeStart + 1, $nameStart - $typeStart - 1);
		$class     = subStr($className, $nameStart + 1);

		return array($module, $type, $class);
	}

	/**
	 * @return string
	 * @param string $module
	 */
	public static function moduleToFolderName($module) {
		if (isset(self::$modules[$module])) {
			return self::$modules[$module];
		}

		self::$modules[$module] = preg_replace('/(.)([A-Z])/', '\\1' . self::MODULE_NAME_SEPARATOR . '\\2', $module);
		self::$modules[$module] = strToLower(self::$modules[$module]);
		return self::$modules[$module];
	}

	protected static function typeToFolder($type) {
		if (isset(self::$types[$type])) {
			return self::$types[$type];
		}
		throw new Nano_Exception('No such type: ' . $type);
	}

}