<?php

namespace App\Controller;

class CookieTest extends \Nano_C {

	/**
	 * @var string
	 */
	public $name1, $name2;

	/**
	 * @var Cookie
	 */
	protected $cookie;

	public function setAction() {
		if (isSet($_GET['http'])) {
			$this->cookie()->httpOnly(true);
		}

		$this->cookie()->set('name1', 'value1');
		$this->cookie()->set('name2', 'value2');
		$this->viewAction();
	}

	public function eraseAction() {
		if (isSet($_GET['http'])) {
			$this->cookie()->httpOnly(true);
		}

		$this->cookie()->erase('name2');
		$this->viewAction();
	}

	public function viewAction() {
		$this->template = 'view';
		$this->name1    = $this->cookie()->get('name1');
		$this->name2    = $this->cookie()->get('name2');
	}

	/**
	 * @return \Cookie
	 */
	protected function cookie() {
		if (null === $this->cookie) {
			$this->cookie = new \Cookie(\Nano::app()->config->get('web')->domain);
		}
		return $this->cookie;
	}

	protected function after() {
		$this->renderer()->setViewsPath(dirName(__DIR__) . DIRECTORY_SEPARATOR . 'views');
	}

}