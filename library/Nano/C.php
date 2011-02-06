<?php

abstract class Nano_C {

	/**
	 * @var string
	 */
	public $layout = null;

	/**
	 * @var string
	 */
	public $context = Nano_Dispatcher_Context::CONTEXT_DEFAULT;

	/**
	 * @var string
	 */
	public $template = null;

	/**
	 * @var Nano_Dispatcher
	 */
	protected $dispatcher;

	/**
	 * @var boolean
	 */
	protected $rendered = false;

	/**
	 * @var Nano_HelperBroker
	 */
	protected $helper;

	/**
	 * @var Nano_C_Plugin[]
	 */
	protected $plugins = null;

	final public function __construct(Nano_Dispatcher $dispatcher) {
		$this->dispatcher = $dispatcher;
		$this->helper     = Nano::helper();
		$this->plugins    = new SplObjectStorage();
		foreach (Nano::config('plugins') as $class) {
			$this->plugins->attach(new $class);
		}
	}

	/**
	 * @return string
	 * @param string $action
	 */
	public function run($action) {
		$method = Nano_Dispatcher::formatName($action, false);
		$result = null;

		$this->runInit();
		if (false !== $this->runBefore()) {
			try {
				$result = $this->$method();
			} catch (Exception $e) {
				throw $e;
			}
		}
		$this->runAfter();

		if (false === $this->rendered) {
			return $this->render(null, null);
		}
		return $result;
	}

	/**
	 * @return void
	 */
	public function markRendered() {
		$this->rendered = true;
	}

	/**
	 * @return Nano_Dispatcher
	 */
	public function dispatcher() {
		return $this->dispatcher;
	}

	/**
	 * @return string
	 * @param string $name
	 * @param scalar $default
	 */
	public function p($name, $default = null) {
		return $this->dispatcher()->param($name, $default);
	}

	/**
	 * @param string $to
	 * @param int $status
	 */
	public function redirect($to, $status = 302) {
		$this->markRendered();
		header('Location: ' . $to, true, $status);
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
	 * @return string
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

		$this->markRendered();
		if ($this->layout) {
			return Nano_Render::layout($this, $controller, $action);
		} else {
			return Nano_Render::view($this, $controller, $action);
		}
	}

	protected function addPlugin(Nano_C_Plugin $plugin) {
		$this->plugins->attach($plugin);
	}

	/**
	 * @return void
	 */
	protected function runInit() {
		foreach ($this->plugins as $plugin) { /* @var $plugin Nano_C_Plugin */
			$plugin->init($this);
		}
		$this->init();
	}

	/**
	 * @return boolean
	 */
	protected function runBefore() {
		foreach ($this->plugins as $plugin) { /* @var $plugin Nano_C_Plugin */
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
		foreach ($this->plugins as $plugin) { /* @var $plugin Nano_C_Plugin */
			$plugin->after($this);
		}
		$this->after();
	}

	/**
	 * @return void
	 * @param string $message
	 * @throws Nano_Exception
	 */
	protected function pageNotFound($message = null) {
		throw new Nano_Exception(null === $message ? Nano_Dispatcher::ERROR_NOT_FOUND : $message, Nano_Dispatcher::ERROR_NOT_FOUND);
	}

	/**
	 * @return void
	 * @param string $message
	 * @throws Nano_Exception
	 */
	protected function internalError($message = null) {
		throw new Nano_Exception(null === $message ? Nano_Dispatcher::ERROR_INTERNAL : $message, Nano_Dispatcher::ERROR_INTERNAL);
	}

}