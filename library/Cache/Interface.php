<?php

interface Cache_Interface {

	/**
	 * @return mixed
	 * @param string $key
	 */
	public function get($key);

	/**
	 * @return bool
	 * @param string $key
	 * @param mixed $value
	 * @param int $expires
	 * @param string[] $tags
	 */
	public function set($key, $value, $expires, array $tags = array());

	/**
	 * @return bool
	 * @param string $key
	 */
	public function clear($key);

	/**
	 * @return bool
	 * @param string[] $tags
	 */
	public function clearTag(array $tags);

}