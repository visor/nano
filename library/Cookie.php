<?php

class Cookie {

	const ONE_MONTH = 2592000;

	/**
	 * @return scalar
	 * @param string $name
	 * @param scalar $default
	 */
	public static function get($name, $default = null) {
		if (isset($_COOKIE[$name])) {
			return $_COOKIE[$name];
		}
		return $default;
	}

	/**
	 * @return void
	 * @param string $name
	 * @param scalar $value
	 * @param int $expire
	 */
	public static function set($name, $value, $expire = null) {
		if (null === $expire) {
			$expire = self::ONE_MONTH;
		}
		setCookie($name, $value, $_SERVER['REQUEST_TIME'] + $expire, '/', self::domain(), true, true);
		$_COOKIE[$name] = $value;
	}

	/**
	 * @return void
	 * @param string $name
	 */
	public static function erase($name) {
		setCookie($name, null, 0, '/', self::domain(), true, true);
		unset($_COOKIE[$name]);
	}

	/**
	 * @return string
	 */
	protected static function domain() {
//		return '.' . Nano::config('web')->domain;
	}
}