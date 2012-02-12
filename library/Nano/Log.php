<?php

class Nano_Log {

	const NAME = 'app.log';

	/**
	 * @var Application
	 */
	protected $application;

	/**
	 * @param Application $application
	 */
	public function __construct(Application $application) {
		$this->application = $application;
	}

	/**
	 * @param string $string Message to write
	 */
	public function message($string) {
		error_log($string . PHP_EOL, 3, self::getFile());
	}

	/**
	 * Returns log contents
	 *
	 * @return string
	 */
	public function get() {
		if (file_exists(self::getFile())) {
			return file_get_contents(self::getFile());
		}
		return '';
	}

	/**
	 * Clears log file
	 *
	 * @return void
	 */
	public function clear() {
		if (file_exists(self::getFile())) {
			unLink(self::getFile());
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