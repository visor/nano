<?php

namespace CliScript;

class ModuleFiveScript extends \Nano\Cli\Script {

	/**
	 * @return int
	 * @param string[] $args
	 */
	public function run(array $args) {
		echo '[test script was run]', PHP_EOL;
		return 100;
	}

}