<?php

class Cache_API_Fake implements Cache_Interface {

	public
		  $config = null
		, $lastSet = null
		, $lastClear = null
		, $lastClearTag = null
	;

	/**
	 * @return void
	 * @param stdClass $config
	 */
	public function configure(stdClass $config) {
		$this->config = $config;
	}

	/**
	 * @return mixed
	 * @param string $key
	 */
	public function get($key) {
		if (false === strPos($key, 'invalid')) {
			return $key;
		}
		return null;
	}

	/**
	 * @return bool
	 * @param string $key
	 * @param mixed $value
	 * @param int $expires
	 * @param string[] $tags
	 */
	public function set($key, $value, $expires, array $tags = array()) {
		$this->lastSet = array($key, $value, $expires, $tags);
	}

	/**
	 * @return bool
	 * @param string $key
	 */
	public function clear($key = null) {
		$this->lastClear = $key;
	}

	/**
	 * @return bool
	 * @param string[] $tags
	 */
	public function clearTag(array $tags) {
		$this->lastClearTag = $tags;
	}

}