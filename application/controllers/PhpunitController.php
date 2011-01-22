<?php

class PhpunitController extends Nano_C_Cli {

	/**
	 * Runs PHPUnit tests
	 */
	public function indexAction() {
		include 'PHPUnit/Autoload.php';
		$command = new TestUtils_TextUI_Command();
		$command->run($this->args, true);
	}

}