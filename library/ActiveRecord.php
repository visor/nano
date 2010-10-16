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

	/**
	 * @var boolean
	 */
	private $new = null;

	final public function __construct($data = null) {
		$this->checkConfiguration();
		$this->tableName = static::TABLE_NAME;
		$this->setUpData($data);
	}

	/**
	 * @return boolean
	 */
	public function isNew() {
		return $this->new;
	}

	/**
	 * @return array|scalar
	 * @param boolean $forceArray
	 */
	public function getPrimaryKey($forceArray = false) {
		$result = array();
		foreach ($this->primaryKey as $name) {
			$result[$name] = $this->__get($name);
		}
		if (1 == count($result) && false === $forceArray) {
			return current($result);
		}
		return $result;
	}

	/**
	 * @return array
	 */
	public function getChangedData() {
		return array_diff_assoc($this->data, $this->originalData);
	}

	public function __get($field) {
		if ($this->__isset($field)) {
			return $this->data[$field];
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
		return isset($this->data[$field]);
	}

	public function __unset($field) {
		$this->data[$field] = null;
	}

	protected function beforeDelete() {}

	protected function afterDelete() {}

	protected function beforeInsert() {}

	protected function afterInsert() {}

	protected function beforeUpdate() {}

	protected function afterUpdate() {}

	/**
	 * @return array
	 */
	protected function buildInsertFields() {
		$result = array();
		foreach ($this->fields as $field) {
			$result[$field] = $this->__get($field);
		}
		if ($this->autoIncrement) {
			foreach ($this->primaryKey as $field) {
				unset($result[$field]);
			}
		}
		return $result;
	}

	/**
	 * @return array
	 */
	protected function buildUpdateFields() {
		return array_diff($this->getChangedData(), $this->getPrimaryKey(true));
	}

	/**
	 * @return sql_expr
	 */
	protected function buildUpdateCriteria() {
		$result     = sql::expr();
		$primaryKey = $this->getPrimaryKey(true);
		if (in_array(null, $primaryKey)) {
			return $result;
		}
		foreach ($primaryKey as $field => $value) {
			$result->isEmpty()
				? $result->add($field, '=', $value)
				: $result->addAnd($field, '=', $value)
			;
		}
		return $result;
	}

	/**
	 * @return sql_expr
	 */
	protected function buildDeleteCriteria() {
		$result     = sql::expr();
		$primaryKey = $this->getPrimaryKey(true);
		$fields     = in_array(null, $primaryKey) ? $this->data : $primaryKey;
		foreach ($fields as $field => $value) {
			if (null === $value) {
				continue;
			}
			$result->isEmpty()
				? $result->add($field, '=', $value)
				: $result->addAnd($field, '=', $value)
			;
		}
		return $result;
	}

	/**
	 * @return void
	 * @throws ActiveRecord_Exception_AutoIncrementNotDefined|ActiveRecord_Exception_NoFields|ActiveRecord_Exception_NoPrimaryKey|ActiveRecord_Exception_NoTableName
	 */
	private function checkConfiguration() {
		if (!defined(get_class($this) . '::TABLE_NAME')) {
			throw new ActiveRecord_Exception_NoTableName($this);
		}
		if (empty($this->primaryKey) || !is_array($this->primaryKey)) {
			throw new ActiveRecord_Exception_NoPrimaryKey($this);
		}
		if (null === $this->autoIncrement) {
			throw new ActiveRecord_Exception_AutoIncrementNotDefined($this);
		}
		if (empty($this->fields)) {
			throw new ActiveRecord_Exception_NoFields($this);
		}
	}

	/**
	 * @return void
	 * @throws InvalidArgumentException
	 * @param array|null $data
	 */
	private function setUpData($data) {
		if (null === $data) {
			$this->new = true;
			return;
		}
		if (is_array($data)) {
			$this->new = false;
			foreach ($this->fields as $name) {
				if (isset($data[$name])) {
					$this->data[$name] = $this->originalData[$name] = $data[$name];
				}
			}
			return;
		}
		throw new InvalidArgumentException('Invalid data passed to ' . __CLASS__ . ' constructor');
	}

}