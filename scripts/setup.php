<?php

namespace CliScript;

/**
 * @description Builds application configuration and routes files for given settings set
 * @param required $name Name of settings set to build
 */
class Setup extends \Nano\Cli\Script {

	/**
	 * @return int
	 * @param array|\string[] $args
	 */
	public function run(array $args) {
		if (0 == count($args)) {
			$this->stop('Please pass configuration name to setup');
			return 1;
		}

		$builder = new \Nano\Application\Config\Builder($this->getApplication());
		$builder->setSource($this->getApplication()->rootDir . DIRECTORY_SEPARATOR . 'settings');
		$builder->setDestination($this->getApplication()->rootDir . DIRECTORY_SEPARATOR . 'settings');

		$builder->clean();
		$builder->build($args[0]);
		return 0;
	}

}