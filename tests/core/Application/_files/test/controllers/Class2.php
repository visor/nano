<?php

namespace Module\Test\Controller;

class Class2 extends \Nano\Controller {

	public static function name() {
		return __CLASS__;
	}

	/**
	 * @return string
	 */
	public function indexAction() {
		$this->markRendered();
		return self::name();
	}

	public function viewAction() {
		$this->controller = 'class';
		$this->view       = 'view';
	}

}