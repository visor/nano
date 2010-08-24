<?php

class Nano_Form {

	const MODE_VALIDATE_ALL  = 1;
	const MODE_STOP_ON_ERROR = 2;

	/**
	 * @var string[]
	 */
	protected $fields = array();

	/**
	 * @var mixed[string]
	 */
	protected $data = array();

	/**
	 * @var SplObjectStorage
	 */
	protected $validators = array();

	/**
	 * @var string[string]
	 */
	protected $errors = array();

	/**
	 * @var int
	 */
	protected $mode = self::MODE_STOP_ON_ERROR;

	/**
	 * @var boolean
	 */
	protected $isValid = null;

	public function __construct(array $fields) {
		$this->fields = $fields;
	}

	/**
	 * @return mixed[string] Form values
	 */
	public function getValues() {
		return $this->data;
	}

	/**
	 * Fill form with specified values
	 *
	 * @return Nano_Form
	 * @param array $data
	 */
	public function populate(array $data) {
		$this->invalidate();
		$this->data    = array();
		foreach ($data as $field => $value) {
			if (!in_array($field, $this->fields)) {
				continue;
			}
			$this->__set($field, $value);
		}
		return $this;
	}

	/**
	 * @return Nano_Form
	 * @param sting $field
	 * @param Nano_Validator_Interface $validator
	 * @param string $message
	 */
	public function addValidator($field, Nano_Validator_Interface $validator, $message = null) {
		$this->invalidate();
		if (!isset($this->validators[$field])) {
			$this->validators[$field] = array();
		}
		$validator->setMessage($message);
		$this->validators[$field][] = $validator;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function isValid() {
		if (null === $this->isValid) {
			$this->validate();
		}
		return $this->isValid;
	}

	/**
	 * @return Nano_Form
	 */
	public function validate() {
		$this->invalidate();
		$this->isValid = true;
		foreach ($this->validators as $field => $validators) {
			foreach ($validators as $validator) { /* @var $validator Nano_Validator */
				if ($validator->isValid($this->$field)) {
					continue;
				}
				$this->isValid = false;
				$this->addError($field, $validator->getMessage());
				if (self::MODE_STOP_ON_ERROR === $this->mode) {
					break;
				}
			}
		}
		return $this;
	}

	/**
	 * @return string[string][]
	 */
	public function getErrors() {
		return $this->errors;
	}

	/**
	 * @return string[]|null
	 * @param string $field
	 */
	public function getFieldErros($field) {
		if (isset($this->errors[$field])) {
			return $this->errors[$field];
		}
		return null;
	}

	/**
	 * @return Nano_Validator
	 * @param int $value
	 */
	public function setMode($value) {
		$this->mode = $value;
		$this->invalidate();
		return $this;
	}

	public function __isset($field) {
		return isSet($this->data[$field]);
	}

	public function __get($field) {
		if ($this->__isset($field)) {
			return $this->data[$field];
		}
		return null;
	}

	public function __set($field, $value) {
		$value = $this->cleanValue($value);
		if ($this->isEmpty($value)) {
			return;
		}
		$this->data[$field] = $value;
		$this->invalidate();
	}

	/**
	 * @return mixed
	 * @param mixed $value
	 */
	protected function cleanValue($value) {
		if (is_scalar($value)) {
			return trim($value);
		}

		$result = array();
		foreach ($value as $key => $item) {
			$item = $this->cleanValue($item);
			if ($this->isEmpty($item)) {
				continue;
			}
			$result[$key] = $item;
		}
		if (0 == count($result)) {
			return null;
		}
		return $result;
	}

	/**
	 * @return boolean
	 * @param mixed $value
	 */
	protected function isEmpty($value) {
		if (is_array($value)) {
			return 0 === count($value);
		}
		return 0 === strLen(trim($value));
	}

	/**
	 * @return void
	 */
	protected function invalidate() {
		$this->isValid = null;
		$this->errors = array();
	}

	/**
	 * @return void
	 * @param string $field
	 * @param string $message
	 */
	protected function addError($field, $message) {
		if (!isset($this->errors[$field])) {
			$this->errors[$field] = array();
		}
		$this->errors[$field][] = $message;
	}

}