<?php

class Nano_Render {

	const VIEW_DIR   = 'views';
	const LAYOUT_DIR = 'layouts';

	/**
	 * @var Application
	 */
	protected $application;

	/**
	 * @var string
	 */
	protected $viewsPath, $moduleViewsDirName, $layoutsPath;

	/**
	 * @var boolean
	 */
	protected $useApplicationDirs = false;

	/**
	 * @param Application $application
	 */
	public function __construct(Application $application) {
		$this->application = $application;
	}

	/**
	 * @return string
	 * @param Nano_C $object
	 */
	public function render(Nano_C $object) {
		$module    = $object->getModule();
		$variables = get_object_vars($object);
		$content   = $this->renderView($module, $object->controller, $object->template, $object->context, $variables);

		if (null === $object->layout) {
			return $content;
		}

		$variables['content'] = $content;
		$layoutFile = $this->getLayoutFileName($object->layout, $object->context);
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

		if ($this->useApplicationDirs) {
			$viewName = $this->viewsPath . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $controller . DIRECTORY_SEPARATOR . $action;
		} else {
			$viewName = $this->application->modules->getPath($module, $this->moduleViewsDirName . DIRECTORY_SEPARATOR . $controller . DIRECTORY_SEPARATOR . $action);
		}
		return $this->addContext($viewName, $context) . '.php';
	}

	/**
	 * @return string
	 * @param string $layout
	 * @param string|null $context
	 */
	public function getLayoutFileName($layout, $context = null) {
		return $this->addContext($this->layoutsPath . DIRECTORY_SEPARATOR . $layout, $context) . '.php';
	}

	/**
	 * @return null|string
	 *
	 * @param Nano_Render $renderer
	 * @param string      $fileName
	 * @param array       $variables
	 *
	 * @throws \Exception|\Nano_Exception
	 */
	protected static function file(Nano_Render $renderer, $fileName, array $variables = array()) {
		if (!file_exists($fileName)) {
			throw new Nano_Exception('View ' . $fileName . ' not exists');
		}

		ob_start();
		try {
			extract($variables);

			$application = $renderer->application;
			$helper      = $renderer->application->helper;
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
		if (Nano_Dispatcher_Context::CONTEXT_DEFAULT != $context && null !== $context) {
			$result .= '.' . $context;
		}
		return $result;
	}

}