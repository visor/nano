<?php
/**
 * @description Example application script. Prints configured application modules
 */

namespace CliScript;

class AnExample extends \Nano_Cli_Script {

	/**
	 * @param string[] $args
	 * @return void
	 */
	public function run(array $args) {
		if (0 == \Application::current()->getModules()->count()) {
			echo 'No application modules', PHP_EOL;
			return;
		}

		echo 'Application modules:', PHP_EOL;
		foreach (\Application::current()->getModules()->count() as $name => $path) {
			echo ' - ', $name, '    ', $path, PHP_EOL;
		}
	}

}