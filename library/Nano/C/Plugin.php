<?php

interface Nano_C_Plugin {

	/**
	 * @return void
	 * @param Nano_C $controller
	 */
	public function init(Nano_C $controller);

	/**
	 * @return boolean
	 * @param Nano_C $controller
	 */
	public function before(Nano_C $controller);

	/**
	 * @return void
	 * @param Nano_C $controller
	 */
	public function after(Nano_C $controller);

}