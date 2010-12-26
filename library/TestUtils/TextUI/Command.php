<?php

class TestUtils_TextUI_Command extends PHPUnit_TextUI_Command {

	protected function handleArguments(array $argv) {
		parent::handleArguments($argv);

		$verbose = isset($this->arguments['verbose']) ? true : false;
		$this->arguments['printer'] = new TestUtils_TextUI_ResultPrinter(null, $verbose, false, false);
	}

}