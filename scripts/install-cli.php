<?php

namespace CliScript;

/**
 * @description Installs Nano CLI script
 */
class InstallCli extends \Nano_Cli_Script {

	const BIN = 'nano';

	/**
	 * @return boolean
	 */
	public function needApplication() {
		return false;
	}

	/**
	 * @return void
	 * @param array $args
	 */
	public function run(array $args) {
		$this->createScriptFile(
			$this->getInstallPath(/*isSet($args[1]) ? true : false*/true)
		);
		$this->cli->help();
	}

	/**
	 * @return string
	 * @param boolean $useDefault
	 */
	protected function getInstallPath($useDefault) {
		$pathes = explode(PATH_SEPARATOR, $_SERVER['PATH']);
		if ($useDefault) {
			foreach ($pathes as $path) {
				if (is_dir($path) && is_writable($path)) {
					return $path;
				}
			}
			echo 'Cannot write into $PATH directories', PHP_EOL;
			exit(1);
		}
		return $pathes[0];
	}

	/**
	 * @param string $path
	 * @return void
	 */
	protected function createScriptFile($path) {
		$source = \Nano_Cli::isWindows()
			? $this->getWindowsScriptSource()
			: $this->getUnixScriptSource()
		;
		$fileName = $path . DIRECTORY_SEPARATOR . self::BIN;
		if (\Nano_Cli::isWindows()) {
			$fileName .= '.bat';
		}
		file_put_contents($fileName, $source);
		chMod($fileName, 0755);
	}

	/**
	 * @return string
	 */
	protected function getUnixScriptSource() {
		return
			'#!' . \Nano_Cli::getPhpBinary() . PHP_EOL
			. '<?php include \'' . \Nano_Cli::getCliScriptPath() . '\';'
		;
	}

	/**
	 * @return string
	 */
	protected function getWindowsScriptSource() {
		return
			'@echo off' . PHP_EOL
			. \Nano_Cli::getPhpBinary() . ' ' . \Nano_Cli::getCliScriptPath() . ' %*'
		;
	}

}