<?php

abstract class Form {

	/**
	 * Arrays of valitator methods
	 *
	 * @var array
	 */
	protected $validators = array();

	/**
	 * Array of setters for DbObject
	 *
	 * @var array
	 */
	protected $setters = array();

	/**
	 * Form data
	 *
	 * @var array
	 */
	protected $data = null;

	/**
	 * @var Nano_DbObject
	 */
	protected $object = null;

	/**
	 * @var boolean
	 */
	protected $valid  = false;

	public function __construct(array $data = array()) {
		$this->data = $this->cleanData($data);
	}

	/**
	 * @param Nano_DbObject $object
	 * @return Form
	 */
	public function setObject(Nano_DbObject $object) {
		$this->object = $object;
		return $this;
	}

	/**
	 * @return Nano_DbObject
	 */
	public function getObject() {
		return $this->object;
	}

	/**
	 * @return Form
	 */
	public function validate() {
		if (!$this->valid) {
			foreach ($this->validators as $method) {
				$this->$method();
			}
			$this->valid = true;
		}
		return $this;
	}

	/**
	 * @return Form
	 */
	public function save() {
		if (!$this->valid) {
			$this->validate();
		}
		$this->initObject();
		foreach ($this->setters as $method) {
			$this->$method();
		}
		$this->getObject()->save();
		$this->afterSave();
		return $this;
	}

	protected function initObject() {
		if (null === $this->object) {
			$this->setObject($this->defaultObject());
		}
	}

	/**
	 * @return Nano_DbObject
	 */
	abstract protected function defaultObject();

	protected function afterSave() {}

	protected function cleanData($data) {
		$result = array();
		foreach ($data as $key => $value) {
			$value = is_array($value) ? $this->cleanData($value) : trim($value);
			if (is_array($value) && empty($value)) {
				continue;
			}
			if (is_scalar($value) && '' === $value) {
				continue;
			}
			$result[$key] = $value;
		}
		return $result;
	}
}