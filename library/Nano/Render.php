<?php

class Nano_Render {

	/**
	 * @return string
	 * @param Nano_C $object
	 * @param string $controller
	 * @param string $action
	 */
	public static function layout(Nano_C $object, $controller, $action) {
		$variables = get_object_vars($object);
		$content   = self::view($object, $controller, $action);
		$head      =
			  self::file(self::getFileName($controller, 'controller.head'), $variables, false)
			. self::file(self::getFileName($controller, $action . '.head'), $variables, false)
		;
		$fileName  = LAYOUTS . DS . $object->layout . '.php';

		$variables['content']    = $content;
		$variables['head']       = $head;
		$variables['controller'] = $controller;
		$variables['action']     = $action;
		return self::file($fileName, $variables);
	}

	/**
	 * @return string
	 * @param Nano_C $object
	 * @param string $controller
	 * @param string $action
	 */
	public static function view(Nano_C $object, $controller, $action) {
		$fileName  = self::getFileName($controller, $action);
		$variables = get_object_vars($object);

		$variables['controller'] = $controller;
		$variables['action']     = $action;
		return self::file($fileName, $variables);
	}

	/**
	 * @return string
	 * @param string $controller
	 * @param string $action
	 * @param array $variables
	 * @param boolean $throw
	 */
	public static function script($controller, $action, array $variables = array(), $throw = true) {
		return self::file(self::getFileName($controller, $action), $variables, $throw);
	}

	public static function file($fileName, array $variables = array(), $throw = true) {
		if (!file_exists($fileName)) {
			if ($throw) {
				throw new Exception('View ' . $fileName . ' not exists');
			}
			return null;
		}

		extract($variables);
		$helper = Nano_HelperBroker::instance();

		ob_start();
		include($fileName);
		$result = ob_get_contents();
		ob_end_clean();

		return $result;
	}

	/**
	 * @return string
	 * @param string $controller
	 * @param string $action
	 */
	public static function getFileName($controller, $action) {
		return VIEWS . DS . $controller . DS . $action . '.php';
	}

}