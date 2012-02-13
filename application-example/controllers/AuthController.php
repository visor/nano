<?php

class AuthController extends Nano_C {

	public $layout = false;

	public function loginAction() {
		$this->application()->message->load('control-panel');

		$this->pageTitle = $this->dispatcher()->application()->message->m('cp-login');
//TODO: Use assets module
//		Assets::style()
//			->variable('images', WEB_URL . '/resources/images')
//			->append($this->application()->publicDir . '/resources/styles/960.css')
//			->append($this->application()->publicDir . '/resources/styles/reset.css')
//			->append($this->application()->publicDir . '/resources/styles/text.css')
//			->append($this->application()->publicDir . '/resources/styles/login.css')
//		;
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