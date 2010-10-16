<?php

class Cache {

	private static $instance = null;

	/**
	 * @return Cache_Interface
	 */
	public static function instance() {
		if (null === self::$instance) {
			$api   = Nano::config('cache')->api;
			$class = 'Cache_API_' . $api;
			self::$instance = new $class;
		}
		return self::$instance;
	}

	/**
	 * @return mixed
	 * @param string $key
	 */
	public static function get($key) {
		return self::instance()->get($key);
	}

	/**
	 * @return bool
	 * @param string $key
	 * @param mixed $value
	 * @param int $expires
	 * @param string[] $tags
	 */
	public static function set($key, $value, $expires, array $tags = array()) {
		return self::instance()->set($key, $value, $expires, $tags);
	}

	/**
	 * @return bool
	 * @param string $key
	 */
	public static function clear($key) {
		return self::instance()->clear($key);
	}

	/**
	 * @return bool
	 * @param string[] $tags
	 */
	public static function clearTag(array $tags) {
		return self::instance()->clearTag($tags);
	}

}