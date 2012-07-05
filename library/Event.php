<?php

namespace Nano;

class Event {

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var \ArrayObject
	 */
	protected $arguments;

	/**
	 * @return Event
	 * @param string $type
	 */
	public static function create($type) {
		return new self($type);
	}

	/**
	 * @param string $type
	 */
	public function __construct($type) {
		$this->type      = $type;
		$this->arguments = new \ArrayObject();
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @return Event
	 * @param string $name
	 * @param mixed $value
	 */
	public function setArgument($name, $value) {
		$this->arguments->offsetSet($name, $value);
		return $this;
	}

	/**
	 * @return mixed
	 * @param string $name
	 * @param mixed $defaultValue
	 */
	public function getArgument($name, $defaultValue = null) {
		if ($this->arguments->offsetExists($name)) {
			return $this->arguments->offsetGet($name);
		}
		return $defaultValue;
	}

	/**
	 * @return Event
	 * @param \Nano\Event\Manager $manager
	 */
	public function trigger(\Nano\Event\Manager $manager) {
		$manager->trigger($this);
		return $this;
	}

}