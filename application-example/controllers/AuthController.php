<?php

class AuthController extends Nano_C {

	public $layout = false;

	public function loginAction() {
		Nano::message()->load('control-panel');

		$this->pageTitle = Nano::message()->m('cp-login');
		Assets::style()
			->variable('images', WEB_URL . '/resources/images')
			->append(WEB_ROOT . '/resources/styles/960.css')
			->append(WEB_ROOT . '/resources/styles/reset.css')
			->append(WEB_ROOT . '/resources/styles/text.css')
			->append(WEB_ROOT . '/resources/styles/login.css')
		;
	}

	public function authAction() {
		return $this->redirect('/cp');
		//check and login
	}

	public function logoutAction() {
		return $this->redirect('/login');
		//User::logout();
	}

}