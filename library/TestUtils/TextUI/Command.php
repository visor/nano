<?php

namespace Nano\TestUtils\TextUI;

class Command extends \PHPUnit_TextUI_Command {

	protected function handleArguments(array $argv) {
		parent::handleArguments($argv);

		$verbose = isset($this->arguments['verbose']) ? true : false;
		$this->arguments['printer'] = new ResultPrinter(null, $verbose, false, false);
	}

}