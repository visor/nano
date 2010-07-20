<?php

class PlaceholderHelper extends Nano_Helper {

	/**
	 * @var string[string]
	 */
	private static $placeholders;

	/**
	 * @return PlaceholderHelper
	 */
	public function invoke($name = null) {
		if (null === $name) {
			return $this;
		}
		return $this->get($name);
	}

	/**
	 * @return void
	 * @param  string $name
	 */
	public function start($name) {
		if (!isset(self::$placeholders[$name])) {
			self::$placeholders[$name] = '';
		}
		ob_start();
	}

	/**
	 * @return void
	 * @param  string $name
	 */
	public function stop($name) {
		self::$placeholders[$name] .= ob_get_clean();
	}

	/**
	 * @return string
	 * @param  $name
	 */
	public function get($name) {
		if (isset(self::$placeholders[$name])) {
			return self::$placeholders[$name];
		}
		return null;
	}

}