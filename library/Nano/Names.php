<?php

namespace Nano;

class Names {

	const NAMESPACE_MODULE     = 'Module\\';
	const NAMESPACE_APP        = 'App\\';
	const NAMESPACE_CONTROLLER = 'Controller\\';
	const NAMESPACE_MODEL      = 'Model\\';
	const NAMESPACE_PLUGIN     = 'Plugin\\';
	const NAMESPACE_HELPER     = 'Helper\\';

	const SUFFIX_ACTION = 'Action';

	/**
	 * @return string
	 * @param string $value
	 */
	public static function common($value) {
		$result = preg_replace('/\s+/', '', trim($value));
		$result = strToLower($result);
		$result = str_replace(array('-', '/'), array(' ', '. '), $result);
		$result = ucWords($result);
		$result = str_replace(array(' ', '.'), array('', '\\'), $result);
		return trim($result);
	}

	/**
	 * @return string
	 * @param string $name
	 * @param string $module
	 */
	public static function controllerClass($name, $module = null) {
		if ($module) {
			return self::NAMESPACE_MODULE . self::common($module) . '\\' . self::NAMESPACE_CONTROLLER . self::common($name);
		}
		return self::NAMESPACE_APP . self::NAMESPACE_CONTROLLER . self::common($name);
	}

}