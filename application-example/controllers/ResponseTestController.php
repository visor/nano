<?php

class ResponseTestController extends Nano_C {

	public function setBodyAction() {
		$this->response()
			->setBody('foo')
			->appendToBody('bar')
			->send();
		;
		$this->markRendered();
	}

	public function renderBodyAction() {
	}

	public function headerAction() {
		$this->response()->addHeader('X-Test-Controller', 'response');
		$this->response()->send();
		$this->markRendered();
	}

	public function notFoundAction() {
		$this->pageNotFound('always');
	}

	public function errorAction() {
		$this->internalError('always');
	}

	protected function after() {
		$this->renderer()->setViewsPath(dirName(__DIR__) . DIRECTORY_SEPARATOR . 'views');
	}

}