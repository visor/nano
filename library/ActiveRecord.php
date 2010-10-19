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
	 * @var int
	 */
	protected $selectLimit = null;

	/**
	 * @var int
	 */
	protected $selectOffset = null;

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
	 * @return ActiveRecord
	 */
	public static function create() {
		return new static(null);
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
			reset($this->primaryKey);
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

	/**
	 * @return void
	 */
	public function save() {
		if ($this->isNew()) {
			$this->insert();
			return;
		}
		$this->update();
	}

	/**
	 * @return void
	 */
	public function delete() {
		$where = $this->buildDeleteCriteria();
		$this->beforeDelete();
		Nano::db()->delete($this->tableName, $where->toString(Nano::db()));
		$this->afterDelete();
	}

	/**
	 * @return ActiveRecord
	 * @param mixed $primaryKey
	 */
	public function findOne($primaryKey = null) {
		$result = ActiveRecord_Storage::load(
			  $this
			, $this->getSelectQuery($this->buildSelectCriteria($primaryKey))->limit(1, 0)
		);
		return $result->fetch();
	}

	/**
	 * @return Nano_Db_Statement
	 * @param array $params
	 */
	public function find(array $params = null) {
		if (null === $params) {
			$params = $this->getChangedData();
		}
		$expr = sql::expr();
		foreach ($params as $param => $value) {
			$expr->isEmpty()
				? $expr->add($param, '=', $value)
				: $expr->addAnd($param, '=', $value)
			;
		}
		return $this->select($expr);
	}

	/**
	 * @return ActiveRecord
	 * @param int $limit
	 * @param int $offset
	 */
	public function setLimit($limit, $offset = null) {
		$this->selectLimit = $limit;
		$this->selectOffset = $offset;
		return $this;
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

	/**
	 * @return void
	 */
	protected function beforeDelete() {}

	/**
	 * @return void
	 */
	protected function afterDelete() {}

	/**
	 * @return void
	 */
	protected function beforeInsert() {}

	/**
	 * @return void
	 */
	protected function afterInsert() {}

	/**
	 * @return void
	 */
	protected function beforeUpdate() {}

	/**
	 * @return void
	 */
	protected function afterUpdate() {}

	/**
	 * @return sql_select
	 */
	protected function getSelectQuery(sql_expr $expr = null) {
		$result = sql::select(sql::ALL)->from($this->tableName);
		if (null !== $expr && !$expr->isEmpty()) {
			$result->where($expr);
		}
		return $result;
	}

	/**
	 * @param sql_expr $expr
	 * @return Nano_Db_Statement
	 */
	protected function select(sql_expr $expr) {
		$query = $this->getSelectQuery($expr);
		if ($this->selectLimit) {
			$query->limit($this->selectLimit, $this->selectOffset);
		}
		return ActiveRecord_Storage::load($this, $query);
	}

	/**
	 * @return void
	 */
	protected function insert() {
		$this->beforeInsert();
		Nano::db()->insert($this->tableName, $this->buildInsertFields());
		if ($this->autoIncrement && 1 == count($this->primaryKey)) {
			$name = current($this->primaryKey);
			$this->{$name} = Nano::db()->lastInsertId();
		}
		$this->new = false;
		$this->afterInsert();
	}

	/**
	 * @return void
	 */
	protected function update() {
		$where  = $this->buildUpdateCriteria();
		if ($where->isEmpty()) {
			return;
		}
		$fields = $this->buildUpdateFields();
		if (empty($fields)) {
			return;
		}

		$this->beforeUpdate();
		Nano::db()->update($this->tableName, $fields, $where->toString(Nano::db()));
		$this->afterUpdate();
	}

	/**
	 * @return sql_expr
	 * @param mixed $params
	 */
	protected function buildSelectCriteria($params) {
		$result = sql::expr();
		foreach ($this->buildWhereFields($params) as $field => $value) {
			$result->isEmpty()
				? $result->add($field, '=', $value)
				: $result->addAnd($field, '=', $value)
			;
		}
		return $result;
	}

	/**
	 * @return mixed
	 * @param mixed $data
	 */
	protected function buildWhereFields($data) {
		if (null === $data) {
			if (null === $this->getPrimaryKey()) {
				return array();
			}
			return $this->getPrimaryKey(true);
		}
		if (is_scalar($data)) {
			if (1 == count($this->primaryKey)) {
				reset($this->primaryKey);
				return array(current($this->primaryKey) => $data);
			} else {
				return $this->getPrimaryKey(true);
			}
		}
		return $data;
	}

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