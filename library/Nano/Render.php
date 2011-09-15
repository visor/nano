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
		$viewFile  = $this->getViewFileName($object->controller, $object->template, $object->context, $module);
		$variables = get_object_vars($object);
		$content   = self::file($this, $viewFile, $variables);

		if (null === $object->layout) {
			return $content;
		}

		$variables['content'] = $content;
		$layoutFile = $this->getLayoutFileName($object->layout, $object->context);
		return self::file($this, $layoutFile, $variables);
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
		$viewName = $this->application->getModules()->getPath($module, $this->moduleViewsDirName . DIRECTORY_SEPARATOR . $controller . DIRECTORY_SEPARATOR . $action);
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
	 * @param Nano_Render $renderer
	 * @param string $fileName
	 * @param array $variables
	 * @throws Nano_Exception
	 */
	protected static function file(Nano_Render $renderer, $fileName, array $variables = array()) {
		if (!file_exists($fileName)) {
			throw new Nano_Exception('View ' . $fileName . ' not exists');
		}

		ob_start();
		try {
			extract($variables);
			$helper = Nano::helper();
			include($fileName);
			$result = ob_get_contents();
			ob_end_clean();
			return $result;
		} catch (Exception $e) {
			ob_end_clean();
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