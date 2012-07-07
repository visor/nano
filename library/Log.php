<?php

namespace Nano;

class Log {

	const NAME = 'app.log';

	/**
	 * @var \Nano\Application
	 */
	protected $application;

	/**
	 * @param \Nano\Application $application
	 */
	public function __construct(\Nano\Application $application) {
		$this->application = $application;
	}

	/**
	 * @param string $string Message to write
	 */
	public function message($string) {
		error_log($string . PHP_EOL, 3, $this->getFile());
	}

	/**
	 * Returns log contents
	 *
	 * @return string
	 */
	public function get() {
		if (file_exists($this->getFile())) {
			return file_get_contents($this->getFile());
		}
		return '';
	}

	/**
	 * Clears log file
	 *
	 * @return void
	 */
	public function clear() {
		if (file_exists($this->getFile())) {
			unLink($this->getFile());
		}
	}

	/**
	 * Return log file name. Defined in application/settings/log.php
	 *
	 * @return string
	 */
	public function getFile() {
		return $this->application->rootDir . DS . self::NAME;
	}

}