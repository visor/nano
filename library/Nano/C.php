<?php

abstract class Nano_C {

	const CONTEXT_DEFAULT = 'default';

	/**
	 * @var string
	 */
	public $layout = null;

	/**
	 * @var string
	 */
	public $controller = null;

	/**
	 * @var string
	 */
	public $action = null;

	/**
	 * @var string
	 */
	public $template = null;

	/**
	 * @var string
	 */
	public $context = self::CONTEXT_DEFAULT;

	/**
	 * @var string
	 */
	protected $module = null;

	/**
	 * @var \Nano\Application
	 */
	protected $application;

	/**
	 * @var boolean
	 */
	protected $rendered = false;

	/**
	 * @var Nano_Render
	 */
	protected $renderer = null;

	/**
	 * @var Nano_C_Response
	 */
	protected $response = null;

	/**
	 * @var Nano_C_Redirect
	 */
	protected $redirect = null;

	/**
	 * @return string|null
	 */
	public function getModule() {
		if (null === $this->module && \Nano\Classes::isModuleClass($className = get_class($this))) {
			list(, $this->module, ) = explode(NS, $className, 3);
			$this->module = Nano::app()->modules->nameToFolder($this->module);
		}
		return $this->module;
	}

	/**
	 * @return void
	 * @param string $action
	 */
	public function run($action) {
		$method = \Nano\Dispatcher::formatName($action, false);
		$result = null;

		$this->createResponse();
		$this->runInit();
		if (false !== $this->runBefore()) {
			$this->$method();
		}
		$this->runAfter();

		if (false === $this->rendered) {
			$this->render(null, null);
			$this->response()->send();
		} elseif ($this->response()->isModified()) {
			$this->response()->send();
		}
	}

	/**
	 * @return void
	 */
	public function markRendered() {
		$this->rendered = true;
	}

	/**
	 * @return \Nano\Dispatcher
	 */
	public function dispatcher() {
		return Nano::app()->dispatcher;
	}

	/**
	 * @return string
	 * @param string $name
	 * @param mixed $default
	 */
	public function p($name, $default = null) {
		return Nano::app()->dispatcher->param($name, $default);
	}

	/**
	 * @return Nano_C_Redirect
	 * @param null|string $to
	 * @param int $status
	 */
	public function redirect($to = null, $status = 302) {
		if (null === $this->redirect) {
			$this->redirect = new Nano_C_Redirect($this->response());
		}
		$this->markRendered();
		if (null === $to) {
			return $this->redirect;
		}
		$this->redirect->to($to, $status);
		return $this->redirect;
	}

	/**
	 * @return Nano_C_Response
	 */
	public function response() {
		return $this->response;
	}

	/**
	 * @return void
	 * @param Nano_C_Response $value
	 */
	public function setResponse(Nano_C_Response $value) {
		$this->response = $value;
	}

	/**
	 * @return Nano_Render
	 */
	public function renderer() {
		if (null === $this->renderer) {
			$this->renderer = $this->createRenderer();
			$this->configureRenderer();
		}
		return $this->renderer;
	}

	/**
	 * @return void
	 * @param Nano_Render $value
	 */
	public function setRenderer(Nano_Render $value) {
		$this->renderer = $value;
		$this->configureRenderer();
	}

	/**
	 * @return void
	 */
	protected function init() {}

	/**
	 * @return void|boolean
	 */
	protected function before() {}

	/**
	 * @return void
	 */
	protected function after() {}

	/**
	 * @return void
	 */
	protected function runContextAction() {
		if (!$this->context) {
			return;
		}
		$method = \Nano\Dispatcher::formatName($this->dispatcher()->action() . '-' . $this->context, false);
		if (!method_exists($this, $method)) {
			return;
		}
		$this->$method();
	}

	/**
	 * @return void
	 * @param string $controller
	 * @param string $action
	 */
	protected function render($controller = null, $action = null) {
		if (null === $controller) {
			$controller = $this->dispatcher()->controller();
		}
		if (null === $action) {
			$action = $this->template ? $this->template : $this->dispatcher()->action();
		}

		$this->controller = $controller;
		$this->template   = $action;
		$this->action     = $action;

		$this->response()->setBody($this->renderer()->render($this));
		$this->markRendered();
	}

	/**
	 * @return Nano_Render
	 */
	protected function createRenderer() {
		return new Nano_Render(Nano::app());
	}

	/**
	 * @return void
	 */
	protected function configureRenderer() {
		$this->renderer->setLayoutsPath(Nano::app()->rootDir . DIRECTORY_SEPARATOR . Nano_Render::LAYOUT_DIR);
		$this->renderer->setViewsPath(Nano::app()->rootDir . DIRECTORY_SEPARATOR . Nano_Render::VIEW_DIR);
		$this->renderer->setModuleViewsDirName(Nano_Render::VIEW_DIR);
	}

	/**
	 * @return void
	 */
	protected function createResponse() {
		if (null !== $this->response) {
			return;
		}
		$this->response = new Nano_C_Response(Nano::app());
	}

	/**
	 * @return void
	 */
	protected function runInit() {
		foreach (Nano::app()->plugins as $plugin) { /* @var $plugin Nano_C_Plugin */
			$plugin->init($this);
		}
		$this->init();
	}

	/**
	 * @return boolean
	 */
	protected function runBefore() {
		foreach (Nano::app()->plugins as $plugin) { /* @var $plugin Nano_C_Plugin */
			if (false === $plugin->before($this)) {
				return false;
			}
		}
		if (false === $this->before()) {
			return false;
		}
		return true;
	}

	/**
	 * @return void
	 */
	protected function runAfter() {
		foreach (Nano::app()->plugins as $plugin) { /* @var $plugin Nano_C_Plugin */
			$plugin->after($this);
		}
		$this->after();
	}

	/**
	 * @return null
	 * @param string $message
	 * @throws \Nano\Exception\NotFound
	 */
	protected function pageNotFound($message = null) {
		$this->markRendered();
		Nano::app()->errorHandler()->notFound($message);
	}

	/**
	 * @return null
	 * @param string $message
	 * @throws \Nano\Exception\InternalError
	 */
	protected function internalError($message = null) {
		$this->markRendered();
		Nano::app()->errorHandler()->notFound($message);
	}

}