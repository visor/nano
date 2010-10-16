<?php

class Cache {

	private static $instance = null;

	/**
	 * @return Cache_Interface
	 */
	public static function instance() {
		if (null === self::$instance) {
			self::$instance = self::getApi(Nano::config('cache')->api);

			$config = strToLower(Nano::config('cache')->api);
			if (isset(Nano::config('cache')->{$config})) {
				self::$instance->configure($config);
			}
		}
		return self::$instance;
	}

	/**
	 * @return Cache_Interface
	 * @param string $name
	 */
	public static function getApi($name) {
		try {
			$class = new ReflectionClass('Cache_API_' . $name);
			if (!$class->implementsInterface('Cache_Interface')) {
				throw new Cache_Exception();
			}
			return $class->newInstance();
		} catch (ReflectionException $e) {
			throw new Cache_Exception();
		}
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