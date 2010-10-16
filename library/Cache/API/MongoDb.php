<?php

class Cache_API_MongoDb implements Cache_Interface {

	const COLLECTION        = 'cache';
	const DEFAULT_LIFE_TIME = 86400; // 1day

	/**
	 * @var Mongo
	 */
	protected $mongo = null;

	/**
	 * @var string
	 */
	protected $dataBabase = null;

	/**
	 * @var MongoCollection
	 */
	protected $collection = null;

	/**
	 * @var int
	 */
	protected $lifeTime = self::DEFAULT_LIFE_TIME;

	/**
	 * @var MongoDate
	 */
	protected $expires = null;

	/**
	 * @var int
	 */
	protected $now = null;

	/**
	 * @return void
	 * @param stdClass $config
	 */
	public function configure(stdClass $config) {
		$server  = isset($config->server) ? $config->server : null;
		$options = isset($config->options) ? $config->options : array();
		if (!$server) {
			throw new Cache_Exception();
		}
		$this->dataBabase = baseName($server);
		$this->mongo = new Mongo($server, $options);
		if (isset($config->lifeTime)) {
			$this->lifeTime = $config->lifeTime;
		}
		$this->collection()->ensureIndex(
			  array('key' => 1)
			, array(
				'unique' => true
			)
		);
		$this->collection()->ensureIndex(array('expires' => 1), array());
		$this->collection()->ensureIndex(array('tags' => 1), array());
		$this->now     = time();
		$this->expires = new MongoDate($this->now);
	}

	/**
	 * @return mixed
	 * @param string $key
	 */
	public function get($key) {
		$result = $this->collection()->findOne(array('key' => $key));
		if (!$result) {
			return null;
		}
		if ($result['expires']->sec <= $this->expires->sec) {
			$this->garbage();
			return null;
		}
		if ($result['value'] instanceof MongoBinData) {
			return unSerialize($result['value']->bin);
		}
		return $result['value'];
	}

	/**
	 * @return bool
	 * @param string $key
	 * @param mixed $value
	 * @param int $lifeTime
	 * @param string[] $tags
	 */
	public function set($key, $value, $lifeTime = null, array $tags = array()) {
		$record = array(
			  'key'     => $key
			, 'value'   => is_scalar($value) ? $value : new MongoBinData(serialize($value))
			, 'expires' => new MongoDate($this->now + (null === $lifeTime ? $this->lifeTime : $lifeTime))
			, 'tags'    => $tags
		);
		$this->collection()->insert($record);
	}

	/**
	 * @return bool
	 * @param string $key
	 */
	public function clear($key) {
		$this->collection()->remove(array('key' => $key));
	}

	/**
	 * @return bool
	 * @param string[] $tags
	 */
	public function clearTag(array $tags) {
		$this->collection()->remove(array('tags' => array('$in' => $tags)));
	}

	/**
	 * @return MongoCollection
	 */
	public function collection() {
		if (!$this->collection) {
			$this->collection = $this->mongo->selectCollection($this->dataBabase, self::COLLECTION);
		}
		return $this->collection;
	}

	/**
	 * @return void
	 */
	public function garbage() {
		$this->collection()->remove(array('expires' => array('$lte' => $this->expires)));
	}

}