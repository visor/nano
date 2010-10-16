<?php

abstract class ActiveRecord {

	/**
	 * @var string
	 */
	protected $tableName = null;

	/**
	 * @var boolean
	 */
	protected $autoIncrement = null;

	/**
	 * @var array
	 */
	protected $primaryKey = null;

	/**
	 * @var array
	 */
	protected $fields = null;

	/**
	 * @var array
	 */
	protected $data = array();

	/**
	 * @var array
	 */
	protected $originalData = array();

	public function __construct(array $data = array()) {
		$this->checkConfiguration();
		$this->tableName = static::TABLE_NAME;
		$this->setUpData($data);
	}

	public function getChangedData() {
		return array_diff_assoc($this->data, $this->originalData);
	}

	public function isNew() {
		//
	}

	public function __get($field) {
		if ($this->__isset($field)) {
			return $this->fields[$field];
		}
		return null;
	}

	public function __set($field, $value) {
		if (!in_array($field, $this->fields)) {
			throw new ActiveRecord_Exception_UnknownField($field, $this);
		}
		if ($this->__get($field) === $value) {
			return;
		}
		$this->data[$field] = $value;
	}

	public function __isset($field) {
		return isset($this->fields[$field]);
	}

	public function __unsset($field) {
		unset($this->fields[$field]); //??original data
	}

	protected function checkConfiguration() {
		if (!defined(get_class($this) . '::TABLE_NAME')) {
			throw new ActiveRecord_Exception_NoTableName($this);
		}
		if (empty($this->primaryKey)) {
			throw new ActiveRecord_Exception_NoPrimaryKey($this);
		}
		if (null === $this->autoIncrement) {
			throw new ActiveRecord_Exception_AutoIncrementNotDefined($this);
		}
		if (empty($this->fields)) {
			throw new ActiveRecord_Exception_NoFields($this);
		}
	}

	protected function setUpData(array $data) {
		foreach ($this->fields as $name) {//todo: add validators for each fields
			if (isset($data[$name])) {
				$this->data[$name] = $this->originalData[$name] = $data[$name];
			}
		}
	}

}