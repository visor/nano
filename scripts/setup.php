<?php

namespace CliScript;

/**
 * @description Builds application configuration and routes files for given settings set
 * @param required $name Name of settings set to build
 */
class Setup extends \Nano_Cli_Script {

	/**
	 * @param string[] $args
	 * @return void
	 */
	public function run(array $args) {
		if (0 == count($args)) {
			$this->stop('Please pass configuration name to setup');
			return;
		}

		$builder = new \Nano_Config_Builder($this->getApplication());
		$builder->setSource($this->getApplication()->rootDir . DIRECTORY_SEPARATOR . 'settings');
		$builder->setDestination($this->getApplication()->rootDir . DIRECTORY_SEPARATOR . 'settings');

		$config = $this->getApplication() . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . 'configuration';
		if (file_exists($config)) {
			$this->getApplication()->config->name();
		}

		$builder->clean();
		$builder->build($args[0]);
	}

}