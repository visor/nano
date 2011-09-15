<?php

class Core_Application_FakePlugin implements Nano_C_Plugin {

	/**
	 * @var boolean
	 */
	protected
		$initInvoked = false
		, $beforeInvoked = false
		, $afterInvoked = false
	;

	/**
	 * @return boolean
	 */
	public function initInvoked() {
		return $this->initInvoked;
	}

	/**
	 * @return boolean
	 */
	public function beforeInvoked() {
		return $this->beforeInvoked;
	}

	/**
	 * @return boolean
	 */
	public function afterInvoked() {
		return $this->afterInvoked;
	}

	/**
	 * @return void
	 * @param Nano_C $controller
	 */
	public function init(Nano_C $controller) {
		$this->initInvoked = true;
	}

	/**
	 * @return boolean
	 * @param Nano_C $controller
	 */
	public function before(Nano_C $controller) {
		$this->beforeInvoked = true;
	}

	/**
	 * @return void
	 * @param Nano_C $controller
	 */
	public function after(Nano_C $controller) {
		$this->afterInvoked = true;
	}

}