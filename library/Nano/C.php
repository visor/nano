<?php

abstract class Nano_C {

	/**
	 * @var string
	 */
	public $layout = null;

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

	final public function __construct(Nano_Dispatcher $dispatcher) {
		$this->dispatcher = $dispatcher;
		$this->helper     = Nano_HelperBroker::instance();
	}

	/**
	 * @return string
	 * @param string $action
	 */
	public function run($action) {
		$class  = new ReflectionClass($this);
		$method = Nano_Dispatcher::formatName($action, false);

		if (!$class->hasMethod($method)) {
			throw new Exception('404');
		}

		$result = $class->getMethod($method)->invoke($this);

		if (false === $this->rendered) {
			return $this->render(null, null);
		}
		return $result;
	}

	/**
	 * @return void
	 */
	protected function init() {}

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

	/**
	 * @return void
	 */
	protected function markRendered() {
		$this->rendered = true;
	}

	/**
	 * @return Nano_Dispatcher
	 */
	protected function dispatcher() {
		return $this->dispatcher;
	}

}