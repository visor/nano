<?php

class Nano_Log {

	const EXT = '.log';

	/**
	 * @var string
	 */
	private static $file = null;

	/**
	 * Writes message to log file
	 *
	 * @param string $string Message to write
	 */
	public static function message($string) {
		if (self::getFile()) {
			error_log($string . PHP_EOL, 3, self::getFile());
		}
	}

	/**
	 * Returns log contents
	 *
	 * @return string
	 */
	public static function get() {
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
	public static function clear() {
		if (self::getFile() && file_exists(self::getFile())) {
			unlink(self::getFile());
		}
	}

	/**
	 * Return log file name. Defined in application/settings/log.php
	 *
	 * @return string
	 */
	public static function getFile() {
		return APP . DS . ENV . self::EXT;
	}

}