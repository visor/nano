<?php

class Cache_Ticket {

	protected $key = null;

	protected $tags = array();

	protected $data = null;

	public function __construct($key, $data = null) {
		$this->key  = $key;
		$this->data = $data;
	}

	/**
	 * @return Cache_Ticket
	 * @param string $key
	 * @param mixed $data
	 */
	public static function create($key, $data) {
		return new self($key, $data);
	}

	/**
	 * @return Cache_Ticket
	 * @param string $key
	 */
	public static function load($key) {
		return self::create($key)->resore();
	}

	/**
	 * @return Cache_Ticket
	 * @param string $name
	 */
	public function tag($name) {
		$this->tags[] = $name;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function valid() {
		return (null !== $this->data);
	}

	/**
	 * @return Cache_Ticket
	 * @param int $expires
	 */
	public function save($expires) {
		Cache::set($this->key, $this->data, $_SERVER['REQUEST_TIME'] + $expires, $this->tags);
		return $this;
	}

	/**
	 * @return Cache_Ticket
	 */
	public function restore() {
		$this->data = Cache::get($this->key);
		return $this;
	}

}