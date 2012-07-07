<?php

class Core_Application_FakePlugin implements \Nano\Controller\Plugin {

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
	 * @param \Nano\Controller $controller
	 */
	public function init(\Nano\Controller $controller) {
		$this->initInvoked = true;
	}

	/**
	 * @return boolean
	 * @param \Nano\Controller $controller
	 */
	public function before(\Nano\Controller $controller) {
		$this->beforeInvoked = true;
	}

	/**
	 * @return void
	 * @param \Nano\Controller $controller
	 */
	public function after(\Nano\Controller $controller) {
		$this->afterInvoked = true;
	}

}