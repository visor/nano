<?php

namespace CliScript;

include_once __DIR__ . '/app.php';

/**
 * @description Creates empty module directory structure
 *
 * @param optional $moduleDir Directory where to create application (default — current working directory)
 */
class Module extends App {

	/**
	 * @return void
	 * @param string[] $args
	 */
	public function run(array $args) {
		$this->path = isSet($args[0]) ? $args[0] : getCwd();
		if (!file_exists($this->path)) {
			mkDir($this->path, 0755, true);
		}
		if (!is_dir($this->path)) {
			$this->stop($this->path . ' is not directory', 1);
		}
		if (!is_writable($this->path)) {
			$this->stop('Cannot write into directory ' . $this->path, 1);
		}

		echo 'Creating module skeleton in ' . $this->path, PHP_EOL;
		$this->defaults = __DIR__ . DIRECTORY_SEPARATOR . 'app';
		$this->createDirectoryStructure();
		echo 'Done.', PHP_EOL;
	}

	protected function createDirectoryStructure() {
		$this->mkDir(\Application::CONTROLLER_DIR_NAME);
		$this->mkDir(\Application::HELPERS_DIR_NAME);
		$this->mkDir(\Application::LIBRARY_DIR_NAME);
		$this->mkDir('migrate');
		$this->mkDir(\Application::MODELS_DIR_NAME);
		$this->mkDir(\Application::PLUGINS_DIR_NAME);
		$this->mkDir('resources' . DIRECTORY_SEPARATOR . 'scripts');
		$this->mkDir('resources' . DIRECTORY_SEPARATOR . 'styles');
		$this->mkDir(\Nano_Render::VIEW_DIR);
	}

}