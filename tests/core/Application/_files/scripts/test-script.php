<?php

namespace App\CliScript;

/**
 * @description Test script to use into test cases @ @ @param
 */
class TestScript extends \Nano\Cli\Script {

	/**
	 * @return int
	 * @param string[] $args
	 */
	public function run(array $args) {
		echo '[test script was run]', PHP_EOL;
		return 100;
	}

}