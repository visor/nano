<?php

abstract class ActiveRecord {

	const REL_CLASS = 'class';
	const REL_TYPE  = 'type';
	const REL_FIELD = 'field';
	const REL_REF   = 'ref';

	const ONE       = 'one';
	const MANY      = 'many';

	/**
	 * @var string[string]
	 */
	private static $operators = array(
		  '='  => '='
		, '>'  => '>'
		, '<'  => '<'
		, '>=' => '>='
		, '<=' => '<='
		, '!'  => '!='
		, '%'  => 'like'
	);

	/**
	 * @var array
	 */
	private static $prototypes = array();

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
	 * @var array[string]
	 */
	protected $relations = array();

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

	final public function __construct($data = null, $loaded = false) {
		$this->checkConfiguration();
		$this->tableName = static::TABLE_NAME;
		$this->setUpData($data, $loaded);
	}

	/**
	 * @return ActiveRecord
	 */
	public static function instance() {
		return clone static::prototype();
	}

	/**
	 * @return ActiveRecord
	 */
	public static function prototype() {
		$name = get_called_class();
		if (isset(self::$prototypes[$name])) {
			return self::$prototypes[$name];
		}
		self::$prototypes[$name] = new static(null, false);
		return self::$prototypes[$name];
	}

	/**
	 * @return boolean
	 */
	public function isNew() {
		return $this->new;
	}

	/**
	 * @return boolean
	 */
	public function isAutoincrement() {
		return $this->autoIncrement;
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
			return reset($result);
		}
		return $result;
	}

	/**
	 * @return ActiveRecord
	 * @param mixed[string] $data
	 */
	public function populate(array $data) {
		foreach ($data as $name => $value) {
			if ($this->fieldExists($name)) {
				$this->__set($name, $value);
			}
		}
		return $this;
	}

	/**
	 * @return array
	 */
	public function getChangedData() {
		return array_diff_assoc($this->data, $this->originalData);
	}

	/**
	 * @return boolean
	 */
	public function canInsert() {
		if ($this->isAutoincrement()) {
			return true;
		}
		$primaryKey = $this->getPrimaryKey(true);
		foreach ($primaryKey as $value) {
			if (null === $value) {
				return false;
			}
		}
		return true;
	}

	/**
	 * @return void
	 */
	public function save() {
		if (!empty($this->relations)) {
			$this->saveRelations();
		}
		if ($this->isNew()) {
			if (!$this->canInsert()) {
				throw new PDOException('Integrity constraint violation');
			}
			$this->insert();
		} else {
			$this->update();
		}
		$this->updateOriginalData();
		ActiveRecord_Relation::updateRelation($this);
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
	 * @param mixed $params
	 */
	public function findOne($params = null) {
		try {
		$result = ActiveRecord_Storage::load(
			  $this
			, $this->getSelectQuery($this->buildSelectCriteria($params))->limit(1, 0)
		);
		return $result->fetch();
		} catch (Exception $e) {
			echo $e;
			throw $e;
		}
	}

	/**
	 * @return int
	 * @param array $params
	 */
	public function count(array $params = null) {
		if (null === $params) {
			$params = $this->getChangedData();
		}
		$expr  = sql::expr();
		foreach ($params as $param => $value) {
			$expr->isEmpty()
				? $expr->add($param, '=', $value)
				: $expr->addAnd($param, '=', $value)
			;
		}
		$query = sql::select('count(*)')->from(Nano::db()->quoteName($this->tableName));
		if (!$expr->isEmpty()) {
			$query->where($expr);
		}
		return (int)Nano::db()->getCell($query);
	}

	/**
	 * @return Nano_Db_Statement
	 * @param array $params
	 */
	public function find(array $params = null) {
		if (null === $params) {
			$params = $this->getNotNullFields();
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
	 * @return string
	 */
	public function getTableName() {
		return $this->tableName;
	}

	/**
	 * @return array
	 */
	public function getFields() {
		return $this->fields;
	}

	/**
	 * @return boolean
	 */
	public function changed() {
		return count($this->getChangedData()) > 0;
	}

	/**
	 * @return boolean
	 * @param string $name
	 */
	public function fieldExists($name) {
		return in_array($name, $this->fields);
	}

	/**
	 * @return boolean
	 * @param string $name
	 */
	public function relationExists($name) {
		return array_key_exists(strToLower($name), $this->relations);
	}

	/**
	 * @param string $name
	 * @return array
	 */
	public function getRelation($name) {
		if ($this->relationExists($name)) {
			return $this->relations[$name];
		}
	}

	/**
	 * @return array
	 */
	public function getRelations() {
		return $this->relations;
	}

	/**
	 * @return array
	 */
	public function getOneRelations() {
		$result = array();
		foreach ($this->relations as $name => $relation) {
			if (self::ONE === $relation[self::REL_TYPE]) {
				$result[$name] = $relation;
			}
		}
		return $result;
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
		if ($this->fieldExists($field)) {
			return $this->data[$field];
		}
		if ($this->relationExists($field) && self::ONE === $this->relations[$field][self::REL_TYPE]) {
			return ActiveRecord_Relation::getRecord($this, $field);
		}
		throw new ActiveRecord_Exception_UnknownField($field, $this);
	}

	public function __set($field, $value) {
		if ($this->fieldExists($field)) {
			if ($this->__isset($field) && $this->data[$field] === $value) {
				return;
			}
			$this->data[$field] = $value;
			if (in_array($field, $this->primaryKey)) {
				ActiveRecord_Relation::updateRelation($this);
			}
			return;
		}
		if ($this->relationExists($field) && self::ONE === $this->relations[$field][self::REL_TYPE]) {
			$className = $this->relations[$field][self::REL_CLASS];
			if ($value instanceof $className) {
				ActiveRecord_Relation::setRecord($this, $field, $value);
				return;
			}
		}
		throw new ActiveRecord_Exception_UnknownField($field, $this);
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
		$result = ActiveRecord_Storage::getSelectQuery($this);
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
		if (!$this->changed()) {
			return;
		}
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
	 * @return array
	 */
	protected function getNotNullFields() {
		$result = array();
		foreach ($this->fields as $name) {
			if ($this->__isset($name)) {
				$result[$name] = $this->__get($name);
			}
		}
		return $result;
	}
	/**
	 * @return sql_expr
	 * @param mixed $params
	 */
	protected function buildSelectCriteria($params) {
		return $this->buildCriteria($this->buildWhereFields($params));
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
		return array_diff_assoc($this->getChangedData(), $this->getPrimaryKey(true));
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
		return $this->buildCriteria($primaryKey);
	}

	/**
	 * @return sql_expr
	 */
	protected function buildDeleteCriteria() {
		$result     = sql::expr();
		$primaryKey = $this->getPrimaryKey(true);
		$fields     = in_array(null, $primaryKey) ? $this->data : $primaryKey;
		return $this->buildCriteria($fields);
	}

	/**
	 * @param array $params
	 * @return sql_expr
	 */
	protected function buildCriteria(array $params) {
		$result = sql::expr();
		foreach ($params as $field => $value) {
			if (null === $value) {
				continue;
			}
			list($operator, $operand) = $this->extractOperator($value);
			$result->isEmpty()
				? $result->add($field, $operator, $operand)
				: $result->addAnd($field, $operator, $operand)
			;
		}
		return $result;
	}

	/**
	 * @return array
	 * @param string $value
	 */
	protected function extractOperator($value) {
		$operator = '=';
		$operand  = $value;
		if (0 == strLen($value)) {
			return array($operator, $operand);
		}
		$test     = $value[0];
		if (isset(self::$operators[$test])) {
			$operator = self::$operators[$test];
			$operand  = subStr($value, 1);
			$test    .= $operand[0];
			if (isset(self::$operators[$test])) {
				$operator = self::$operators[$test];
				$operand  = subStr($operand, 1);
			}
		}
		return array($operator, $operand);
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
		if (empty($this->fields) || !is_array($this->fields)) {
			throw new ActiveRecord_Exception_NoFields($this);
		}
	}

	/**
	 * @return void
	 * @throws InvalidArgumentException
	 * @param array|null $data
	 * @param boolean $loaded
	 */
	private function setUpData($data, $loaded) {
		if (false === $loaded && null === $data) {
			$this->new = true;
			foreach ($this->fields as $name) {
				$this->data[$name] = $this->originalData[$name] = null;
			}
			return;
		}
		if (is_array($data)) {
			$this->new = !$loaded;
			foreach ($this->fields as $name) {
				$this->data[$name] = $this->originalData[$name] = isset($data[$name]) ? $data[$name] : null;
			}
			if (true === $loaded) {
				$this->updateOriginalData();
			}
			return;
		}
		if (true === $loaded) {
			$this->new = false;
			$this->updateOriginalData();
			return;
		}
		throw new InvalidArgumentException('Invalid data passed to ' . __CLASS__ . ' constructor');
	}

	/**
	 * @return void
	 */
	private function updateOriginalData() {
		$this->originalData = $this->data;
	}

	/**
	 * @return void
	 */
	private function saveRelations() {
		foreach ($this->getOneRelations() as $relation => $info) {
			$this->__get($relation)->save();
		}
	}

}