<?php

abstract class Nano_Helper {

	/**
	 * @var Nano_Dispatcher
	 */
	protected $dispatcher;

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

	/**
	 * @return Nano_HelperBroker
	 */
	public function helper() {
		return $this->dispatcher->application()->helper;
	}

}