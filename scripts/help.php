<?php

namespace CliScript;

/**
 * @description Displays script usage
 *
 * @param optional scriptName Name of script to display help
 */
class Help extends \Nano_Cli_Script {

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
		if (empty($args)) {
			$this->stop();
		}

		$name   = $args[0];
		$script = $this->cli->getScript($name);
		if (!$script) {
			$this->stop('Script "' . $name . '" not found', 1);
		}

		echo $script->newInstance($name, $this->cli)->usage();
	}

}