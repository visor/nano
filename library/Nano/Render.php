<?php

class Nano_Render {

	const VIEW_DIR = 'views';

	/**
	 * @return string
	 * @param Nano_C $object
	 * @param string $controller
	 * @param string $action
	 */
	public static function layout(Nano_C $object, $controller, $action) {
		$module    = $object->getModule();
		$variables = get_object_vars($object);
		$content   = self::view($object, $controller, $action);
		$head      =
			  self::file(self::getFileName($controller, 'controller.head', $object->context, $module), $variables, false)
			. self::file(self::getFileName($controller, $action . '.head', $object->context, $module), $variables, false)
		;
		$fileName  = self::addContext(LAYOUTS . DS . $object->layout, $object->context) . '.php';

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
		$module    = $object->getModule();
		$fileName  = self::getFileName($controller, $action, $object->context, $module);
		$variables = get_object_vars($object);

		$variables['controller'] = $controller;
		$variables['action']     = $action;
		return self::file($fileName, $variables, true);
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
		$helper = Nano::helper();

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
	 * @param string $context
	 * @param string $module
	 */
	public static function getFileName($controller, $action, $context = null, $module = null) {
		if (null === $module) {
			return self::addContext(VIEWS . DS . $controller . DS . $action, $context) . '.php';
		}
		return self::addContext(Nano::modules()->getPath($module, self::VIEW_DIR . DS . $controller . DS . $action), $context) . '.php';
	}

	/**
	 * @return string
	 * @param string $path
	 * @param string $context
	 */
	protected static function addContext($path, $context) {
		$result = $path;
		if (Nano_Dispatcher_Context::CONTEXT_DEFAULT != $context && null !== $context) {
			$result .= '.' . $context;
		}
		return $result;
	}

}