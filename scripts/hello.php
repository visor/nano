<?php
/**
 * Example Nano script. Just prints "Hello" string.
 */

namespace CliScript;

class Hello extends \Nano_Cli_Script {

	/**
	 * @param string[] $args
	 * @return void
	 */
	public function run(array $args) {
		echo 'Hello', PHP_EOL;
	}

}