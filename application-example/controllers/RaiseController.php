<?php

class RaiseController extends Nano_C {

	public function okAction() {
	}

	public function fatalErrorAction() {
		return generateFatalError();
	}

	public function exceptionAction() {
		throw new RuntimeException('Exception message');
	}

	public function warningAction() {
	}

	public function noticeAction() {
	}

	public function fatalErrorInViewAction() {
		$this->layout   = 'empty';
		$this->template = 'fatal-error';
	}

	public function compileAction() {
	}

	public function customAction() {
		Nano::app()->errorHandler()->handleError(-1, 'Message from action', __FILE__, __LINE__, array());
	}

	public function notFoundAction() {
		Nano::app()->errorHandler()->notFound('Message from action');
	}

	public function internalErrorAction() {
		Nano::app()->errorHandler()->internalError('Message from action');
	}

	public function nullOutputAction() {
		$this->markRendered();
		ob_end_clean();
		(2 / 0);
	}

}