<?php

class Cache {

	/**
	 * @var Cache_Interface
	 */
	private static $instance = null;

	/**
	 * @return Cache_Interface
	 */
	public static function instance() {
		if (null === self::$instance) {
			self::$instance = self::getApi(Nano::config('cache')->api);

			$config = strToLower(Nano::config('cache')->api);
			if (isSet(Nano::config('cache')->{$config})) {
				self::$instance->configure(Nano::config('cache')->{$config});
			}
		}
		return self::$instance;
	}

	/**
	 * @return void
	 */
	public static function invalidateInstance() {
		self::$instance = null;
	}

	/**
	 * @return Cache_Interface
	 * @param string $name
	 */
	public static function getApi($name) {
		try {
			$className = 'Cache_API_' . $name;
			$class = new ReflectionClass($className);
			if (!$class->implementsInterface('Cache_Interface')) {
				throw new Cache_Exception('Invalid cache implementation specified: ' . $className);
			}
			return $class->newInstance();
		} catch (ReflectionException $e) {
			throw new Cache_Exception('Cache implementation ' . $className . ' not found');
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
	public static function clear($key = null) {
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