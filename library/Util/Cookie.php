<?php

namespace Nano\Util;

class Cookie {

	const ONE_MONTH = 2592000;

	/**
	 * @var null|string
	 */
	protected $domain;

	/**
	 * @var boolean
	 */
	protected $secure, $httpOnly = false;

	public function __construct($cookieDomain = null) {
		$this->secure = isSet($_SERVER['HTTPS']) ? true : false;
		$this->domain = $cookieDomain;
	}

	public function httpOnly($value = true) {
		$this->httpOnly = $value;
	}

	/**
	 * @return mixed
	 * @param string $name
	 * @param mixed $default
	 */
	public function get($name, $default = null) {
		if (isset($_COOKIE[$name])) {
			return $_COOKIE[$name];
		}
		return $default;
	}

	/**
	 * @return void
	 * @param string $name
	 * @param mixed $value
	 * @param int $expire
	 */
	public function set($name, $value, $expire = null) {
		if (null === $expire) {
			$expire = self::ONE_MONTH;
		}
		if (!headers_sent()) {
			setCookie($name, $value, $_SERVER['REQUEST_TIME'] + $expire, '/', $this->domain, $this->secure, $this->httpOnly);
		}
		$_COOKIE[$name] = $value;
	}

	/**
	 * @return void
	 * @param string $name
	 */
	public function erase($name) {
		if (!headers_sent()) {
			setCookie($name, null, -1, '/', $this->domain, $this->secure, $this->httpOnly);
		}
		unset($_COOKIE[$name]);
	}

}