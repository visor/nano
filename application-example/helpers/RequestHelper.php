<?php

class RequestHelper extends Nano_Helper {

	const KEY_STATE   = 'state';
	const KEY_URL     = 'current-url';
	const KEY_REFERER = 'referer';

	/**
	 * @var array
	 */
	private static $request = array();

	/**
	 * @var string
	 */
	private static $url = null;

	/**
	 * @var string
	 */
	private static $referer = null;

	/**
	 * @return RequestHelper
	 */
	public function invoke() {
		return $this;
	}

	/**
	 * @return void
	 */
	public function save() {
		$_SESSION[self::KEY_STATE] = $_REQUEST;
	}

	/**
	 * @return boolean
	 */
	public function restore() {
		if (isset($_SESSION[self::KEY_STATE])) {
			self::$request = $_SESSION[self::KEY_STATE];
			unset($_SESSION[self::KEY_STATE]);
			return true;
		}
		return false;
	}

	/**
	 * @return array
	 */
	public function data() {
		return self::$request;
	}

	/**
	 * @return mixed
	 * @param srting $name
	 * @param mixed $default
	 */
	public function get($name, $default = null) {
		if (isset(self::$request[$name])) {
			return self::$request[$name];
		}
		return $default;
	}

	/**
	 * @return void
	 */
	public function saveUrl() {
		if (!isset($_SESSION[self::KEY_URL])) {
			$_SESSION[self::KEY_URL] = $_SERVER['REQUEST_URI'];
		}
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function restoreUrl() {
		self::$url = null;
		if (isset($_SESSION[self::KEY_URL])) {
			self::$url = $_SESSION[self::KEY_URL];
			unset($_SESSION[self::KEY_URL]);
		}
		return self::$url;
	}

	/**
	 * @return void
	 */
	public function saveReferer() {
		if (!isset($_SESSION[self::KEY_REFERER])) {
			$_SESSION[self::KEY_REFERER] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
		}
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function restoreReferer() {
		self::$referer = null;
		if (isset($_SESSION[self::KEY_REFERER])) {
			self::$referer = $_SESSION[self::KEY_REFERER];
			unset($_SESSION[self::KEY_REFERER]);
		}
		return self::$referer;
	}

}