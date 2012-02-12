<?php

class TypedRegistry extends ArrayObject {

	/**
	 * @var string[]
	 */
	private $instances = array();

	/**
	 * @var string[]
	 */
	private $readOnly = array();

	public function __construct() {
		parent::__construct(array(), self::ARRAY_AS_PROPS);
	}

	/**
	 * @return TypedRegistry
	 * @param string $name
	 * @param mixed $value
	 *
	 * @throws InvalidArgumentException
	 */
	public function register($name, $value) {
		$this->offsetSet($name, $value);
		return $this;
	}

	/**
	 * @return TypedRegistry
	 * @param string $name
	 * @param string $instance
	 *
	 * @throws InvalidArgumentException
	 */
	public function ensure($name, $instance) {
		if (isSet($this->instances[$name])) {
			throw new InvalidArgumentException($name . ' is already instance of ' . $this->instances[$name]);
		}

		$this->instances[$name] = $instance;
		if ($this->offsetExists($name)) {
			$this->validateInstance($name, $this->offsetGet($name));
		}
		return $this;
	}

	/**
	 * @return boolean
	 * @param string $name
	 */
	public function isReadOnly($name) {
		return isSet($this->readOnly[$name]);
	}

	/**
	 * @return TypedRegistry
	 * @param string $name
	 * @param mixed $value
	 *
	 * @throws InvalidArgumentException
	 */
	public function readOnly($name, $value = null) {
		if ($this->isReadOnly($name)) {
			throw new InvalidArgumentException($name . ' is already read-only property');
		}

		$this->readOnly[$name] = true;
		if (null !== $value) {
			$this->offsetSet($name, $value);
		}
		return $this;
	}

	/**
	 * @return void
	 * @param string $name
	 * @param mixed $value
	 *
	 * @throws InvalidArgumentException
	 */
	public function offsetSet($name, $value) {
		$this->validateInstance($name, $value);
		if ($this->isReadOnly($name) && $this->offsetExists($name)) {
			throw new InvalidArgumentException($name . ' is read-only property');
		}

		parent::offsetSet($name, $value);
	}

	/**
	 * Prevent appending values without names
	 *
	 * @param mixed $value
	 * @throws RuntimeException
	 */
	public function append($value) {
		throw new RuntimeException('Invalid TypedRegistry usage. Use register() or offsetSet() method.');
	}

	/**
	 * @return void
	 * @param string $name
	 * @param mixed $value
	 *
	 * @throws InvalidArgumentException
	 */
	protected function validateInstance($name, $value) {
		if (!isSet($this->instances[$name])) {
			return;
		}
		if ($value instanceof $this->instances[$name]) {
			return;
		}

		throw new InvalidArgumentException($name . ' should be instance of ' . $this->instances[$name]);
	}

}