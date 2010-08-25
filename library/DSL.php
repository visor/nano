<?php

class DSL {

	protected $parent = null;

	protected $object = null;

	public function __construct(DSL $parent = null, $object = null) {
		$this->parent = $parent;
		$this->object = $object;
	}

	/**
	 * @return mixed
	 * @param string $property
	 */
	public function __get($property) {
		switch ($property) {
			case 'end':
				return $this->parent ? $this->parent : $this->object;
			case 'object':
				return $this->$property;
			default:
				throw new RuntimeException($property);
		}
	}

	/**
	 * @param string $property
	 * @param mixed $value
	 */
	public function __set($property, $value) {
		throw new RuntimeException();
	}

	/**
	 * @return boolean
	 * @param string $property
	 */
	public function __isset($property) {
		switch ($property) {
			case 'object':
				return isset($this->object);
			default:
				return false;
		}
	}

	public function __unset($property) {
		throw new RuntimeException();
	}

	/**
	 * @return DSL
	 * @param string $method
	 * @param array $args
	 */
	public function __call($method, $args) {
		method_exists($this->object, $method)
			? call_user_func_array(array($this->object, $method), $args)
			: $this->object->$method = $args[0];
		return $this;
	}

}