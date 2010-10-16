<?php

class Cache_Ticket {

	/**
	 * @var Cache_Interface
	 */
	private static $cache = null;

	/**
	 * @var string
	 */
	protected $key = null;

	/**
	 * @var string[]
	 */
	protected $tags = array();

	/**
	 * @var mixed
	 */
	protected $data = null;

	public function __construct($key) {
		$this->key = $key;
	}

	/**
	 * @return Cache_Ticket
	 * @param mixed $key
	 */
	public static function create($key) {
		return new static($key);
	}

	/**
	 * @return Cache_Ticket
	 * @param mixed $key
	 */
	public static function load($key) {
		return self::create($key)->restore();
	}

	/**
	 * @return Cache_Ticket
	 * @param mixed $value
	 */
	public function setData($value) {
		$this->data = $value;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getData() {
		return $this->data;
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
	 * @param int $lifeTime
	 */
	public function save($lifeTime = null) {
		self::cache()->clear($this->key);
		self::cache()->set($this->key, $this->data, $lifeTime, $this->tags);
		return $this;
	}

	/**
	 * @return Cache_Ticket
	 */
	public function restore() {
		$this->data = self::cache()->get($this->key);
		return $this;
	}

	/**
	 * @return Cache_Interface
	 */
	public static function cache() {
		if (null === self::$cache) {
			self::setCache(Cache::instance());
		}
		return self::$cache;
	}

	/**
	 * @return void
	 * @param Cache_Interface $cache
	 */
	public static function setCache(Cache_Interface $cache) {
		self::$cache = $cache;
	}

}