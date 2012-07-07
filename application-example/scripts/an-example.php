<?php

namespace CliScript;

/**
 * @description Example application script. Prints configured application modules
 */
class AnExample extends \Nano\Cli\Script {

	/**
	 * @param string[] $args
	 * @return void
	 */
	public function run(array $args) {
		if (0 == $this->getApplication()->modules->count()) {
			echo 'No application modules', PHP_EOL;
			return;
		}

		echo '\Nano\Application modules:', PHP_EOL;
		foreach ($this->getApplication()->modules as $name => $path) {
			echo ' - ', $name, '    ', $path, PHP_EOL;
		}
	}

}