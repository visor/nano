<?php

namespace Nano\Controller;

interface Plugin {

	/**
	 * @return void
	 * @param \Nano\Controller $controller
	 */
	public function init(\Nano\Controller $controller);

	/**
	 * @return boolean
	 * @param \Nano\Controller $controller
	 */
	public function before(\Nano\Controller $controller);

	/**
	 * @return void
	 * @param \Nano\Controller $controller
	 */
	public function after(\Nano\Controller $controller);

}