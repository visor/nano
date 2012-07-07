<?php

namespace CliScript;

/**
 * @description Example Nano script. Just prints "Hello" string.
 */
class Hello extends \Nano\Cli\Script {

	/**
	 * @param string[] $args
	 * @return void
	 */
	public function run(array $args) {
		echo 'Hello', PHP_EOL;
	}

}