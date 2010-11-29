<?php

abstract class Nano_Helper {

	/**
	 * @var Nano_Dispatcher
	 */
	private $dispatcher;

	abstract public function invoke();

	/**
	 * @return string
	 * @param string $folder
	 * @param string $view
	 * @param array $variables
	 */
	protected function render($folder, $view, array $variables = array()) {
		return Nano_Render::script($folder, $view, $variables, true);
	}

	/**
	 * @return Nano_Dispatcher
	 */
	protected function dispatcher() {
		return $this->dispatcher;
	}

	/**
	 * @return void
	 * @param Nano_Dispatcher $dispatcher
	 */
	public function setDispatcher(Nano_Dispatcher $dispatcher = null) {
		$this->dispatcher = $dispatcher;
	}

}