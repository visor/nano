<?php

namespace CliScript;

/**
 * @description Wrapper for default PHPUnit cli-script
 */
class Phpunit extends \Nano\Cli\Script {

	/**
	 * @return boolean
	 */
	public function needApplication() {
		return false;
	}

	/**
	 * @return void
	 * @param string[] $args
	 */
	public function run(array $args) {
		include 'PHPUnit/Autoload.php';
		$command = new \Nano\TestUtils\TextUI\Command();
		$command->run($args, true);
	}

}