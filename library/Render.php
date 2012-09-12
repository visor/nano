<?php

namespace Nano;

class Render {

	const VIEW_DIR   = 'views';
	const LAYOUT_DIR = 'layouts';

	/**
	 * @var string
	 */
	protected $viewsPath, $moduleViewsDirName, $layoutsPath;

	/**
	 * @var boolean
	 */
	protected $useApplicationDirs = true;

	public function __construct() {
	}

	/**
	 * @return string
	 * @param \Nano\Controller $object
	 */
	public function render(\Nano\Controller $object) {
		$variables = get_object_vars($object);
		return $this->renderWithLayout($object->layout, $object->getModule(), $object->controller, $object->template, $object->context, $variables);
	}

	/**
	 * @return null|string
	 * @param string $layout
	 * @param string $module
	 * @param string $controller
	 * @param string $template
	 * @param string $context
	 * @param array $variables
	 */
	public function renderWithLayout($layout, $module, $controller, $template, $context, array $variables) {
		$content = $this->renderView($module, $controller, $template, $context, $variables);

		if (null === $layout) {
			return $content;
		}

		$variables['content'] = $content;
		$layoutFile = $this->getLayoutFileName($module, $layout, $context);
		return self::file($this, $layoutFile, $variables);
	}

	/**
	 * @return null|string
	 * @param string $module
	 * @param string $controller
	 * @param string $template
	 * @param string $context
	 * @param array $variables
	 */
	public function renderView($module, $controller, $template, $context, array $variables) {
		$viewFile = $this->getViewFileName($controller, $template, $context, $module);
		return self::file($this, $viewFile, $variables);
	}

	/**
	 * @param boolean $value
	 */
	public function useApplicationDirs($value) {
		$this->useApplicationDirs = $value;
	}

	/**
	 * @return void
	 * @param string $value
	 */
	public function setViewsPath($value) {
		$this->viewsPath = $value;
	}

	/**
	 * @return void
	 * @param string $value
	 */
	public function setModuleViewsDirName($value) {
		$this->moduleViewsDirName = $value;
	}

	/**
	 * @return void
	 * @param string $value
	 */
	public function setLayoutsPath($value) {
		$this->layoutsPath = $value;
	}

	/**
	 * @return string
	 * @param string $controller
	 * @param string $action
	 * @param string $context
	 * @param string $module
	 */
	public function getViewFileName($controller, $action, $context = null, $module = null) {
		if (null === $module) {
			return $this->addContext($this->viewsPath . DIRECTORY_SEPARATOR . $controller . DIRECTORY_SEPARATOR . $action, $context) . '.php';
		}
		if (false === $this->useApplicationDirs) {
			$viewName = \Nano::app()->modules->getPath($module, $this->moduleViewsDirName . DIRECTORY_SEPARATOR . $controller . DIRECTORY_SEPARATOR . $action);
			return $this->addContext($viewName, $context) . '.php';
		}

		$result = $this->addContext($this->viewsPath . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $controller . DIRECTORY_SEPARATOR . $action, $context) . '.php';
		if (file_exists($result)) {
			return $result;
		}

		$viewName = \Nano::app()->modules->getPath($module, $this->moduleViewsDirName . DIRECTORY_SEPARATOR . $controller . DIRECTORY_SEPARATOR . $action);
		return $this->addContext($viewName, $context) . '.php';
	}

	/**
	 * @return string
	 * @param string $module
	 * @param string $layout
	 * @param string|null $context
	 */
	public function getLayoutFileName($module, $layout, $context = null) {
		$layoutPath = \Nano::app()->rootDir . DS . self::LAYOUT_DIR . DS . $layout;
		if (null === $module) {
			return $this->addContext($layoutPath, $context) . '.php';
		}

		if (true === $this->useApplicationDirs) {
			$applicationLayout = $this->addContext($layoutPath, $context) . '.php';
			if (file_exists($applicationLayout)) {
				return $applicationLayout;
			}
		}

		$moduleLayout = \Nano::app()->modules->getPath($module, self::LAYOUT_DIR . DS . $layout);
		return $this->addContext($moduleLayout, $context) . '.php';
	}

	/**
	 * @return null|string
	 *
	 * @param \Nano\Render $renderer
	 * @param string      $fileName
	 * @param array       $variables
	 *
	 * @throws \Exception|\Nano\Exception
	 */
	protected static function file(\Nano\Render $renderer, $fileName, array $variables = array()) {
		if (!file_exists($fileName)) {
			throw new \Nano\Exception('View ' . $fileName . ' not exists');
		}

		ob_start();
		try {
			extract($variables);

			$application = \Nano::app();
			$helper      = \Nano::app()->helper;
			include($fileName);

			$result = ob_get_contents();
			if (ob_get_level() > 0) {
				ob_end_clean();
			}
			return $result;
		} catch (Exception $e) {
			if (ob_get_level() > 0) {
				ob_end_clean();
			}
			throw $e;
		}
	}

	/**
	 * @return string
	 * @param string $path
	 * @param string $context
	 */
	protected function addContext($path, $context) {
		$result = $path;
		if (\Nano\Controller::CONTEXT_DEFAULT !== $context && null !== $context) {
			$result .= '.' . $context;
		}
		return $result;
	}

}