<?php

class ValidCliController extends Nano_C_Cli {

	public function indexAction() {
		echo 'Hello World!';
	}

	public function simpleAction() {
		echo 'OK';
	}

	public function argsAction() {
		echo $this->args[0];
	}

}