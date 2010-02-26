<?php

abstract class Nano_DbObject {

	protected $table      = null;
	protected $primaryKey = array('id');
	protected $increment  = true;
	protected $properties = array();

	protected $data       = array();

	protected $__isNew    = false;

	public function __construct($row, $create = false) {
		if (is_scalar($this->primaryKey)) {
			$this->primaryKey = array($this->primaryKey);
		}

		if ($this->isPrimaryKey($row) && false === $create) {
			$where = self::db()->buildWhere($this->getPrimaryKey($row));
			$data  = self::db()->getRow('select * from `' . $this->table . '` where ' . $where, PDO::FETCH_ASSOC);
			if (!is_array($data)) {
				$this->__isNew = true;
				return;
			}
			$this->loadData($data);
			$this->__isNew = false;
		} else {
			if (is_array($row)) {
				$this->loadData($row);
			}
			$this->__isNew = true;
		}
		$this->init();
	}

	/**
	 * @return boolean
	 */
	public function isNew() {
		return $this->__isNew;
	}

	/**
	 * @return string
	 */
	public function getTableName() {
		return $this->table;
	}

	public function getPrimaryKey($data = null) {
		if (null === $data) {
			$data = $this->data;
		}

		if (is_scalar($data) && 1 == count($this->primaryKey)) {
			$result = array();
			foreach ($this->primaryKey as $field) {
				$result[$field] = $data;
			}
			return $result;
		}
		$result = array();
		foreach ($this->primaryKey as $field) {
			$result[$field] = isset($data[$field]) ? $data[$field] : null;
		}
		return $result;
	}

	/**
	 * @return Nano_DbObject
	 */
	public function save() {
		$this->beforeSave();

		if ($this->isNew()) {
			$data = $this->data;
			if ($this->increment) {
				foreach ($this->getPrimaryKey() as $field => $value) {
					unset($data[$field]);
				}
			}
			self::db()->insert($this->table, $data);
			$this->updatePrimaryKey();
		} else {
			$where = $this->getPrimaryKey();
			$data = $this->data;
			foreach ($where as $field => $value) {
				unset($data[$field]);
			}
			self::db()->update($this->table, $data, $where);
		}

		$this->afterSave();
		$this->__isNew = false;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function delete() {
		if ($this->isNew()) {
			return false;
		}

		$this->beforeDelete();
		$rows = self::db()->delete($this->table, $this->getPrimaryKey());
		if (1 != $rows) {
			return false;
		}
		$this->afterDelete();

		return true;
	}

	public function isPrimaryKey($data) {
		$pk = $this->getPrimaryKey();
		if (is_scalar($data) && 1 == count($pk)) {
			$temp = array();
			foreach ($pk as $field => $value) {
				$temp[$field] = $data;
			}
			$data = $temp;
		}
		if (null === $data) {
			$data = array();
		}
		$test = array_diff_key($this->getPrimaryKey(), $data);
		if (0 == count($test)){
			return true;
		}
		return false;
	}

	public function toArray() {
		return $this->data;
	}

	/**
	 * @return scalar
	 * @param string $property
	 */
	public function __get($property) {
		if (!in_array($property, $this->properties)) {
			return null;
		}
		return $this->data[$property];
	}

	/**
	 * @return void
	 * @param string $property
	 * @param scalar $value
	 */
	public function __set($property, $value) {
		if (!in_array($property, $this->properties)) {
			return null;
		}
		$this->data[$property] = $value;
	}

	/**
	 * @return Nano_Db
	 */
	protected static function db() {
		return Nano_Db::instance();
	}

	/**
	 * @return Nano_DbObject
	 * @param string $className
	 * @param scalar[string] $row
	 */
	protected static function createNew($className, array $row = array()) {
		return new $className($row, true);
	}

	/**
	 * @return Nano_DbObject
	 * @param string $className
	 * @param int $offset
	 * @param int $limit
	 * @param string $orderBy
	 */
	protected static function getAll($className, $table, $offset, $limit, $orderBy) {
		return self::find($className, $table, null, $offset, $limit, $orderBy);
	}

	protected static function find($className, $table, $where = null, $offset = null, $limit = null, $orderBy = null) {
		$result = array();
		$query = 'select * from ' . $table;
		if (null !== $where) {
			$query .= ' where ' . self::db()->buildWhere($where);
		}
		if (null !== $orderBy) {
			$query .= ' order by ' . $orderBy;
		}
		if (null !== $limit) {
			$query .= ' limit ' . $offset . ', ' . $limit;
		}
		$stmt = self::db()->query($query);
		foreach ($stmt as $row) {
			$result[] = new $className((array)$row);
		}
		return $result;
	}

	/**
	 * @return int
	 * @param string $className
	 * @param int $offset
	 * @param int $limit
	 * @param string $orderBy
	 */
	protected static function countAll($className, $table, array $where = array()) {
		$result = null;
		$query = 'select count(*) from ' . $table;
		if (!empty($where)) {
			$query .= ' where ' . self::db()->buildWhere($where);
		}
		$result = (int)self::db()->getCell($query);
		return $result;
	}

	protected function beforeSave() {}
	protected function afterSave() {}
	protected function beforeDelete() {}
	protected function afterDelete() {}

	protected function updatePrimaryKey() {
		$pk = $this->getPrimaryKey();
		if (1 == count($pk) && true === $this->increment) {
			foreach ($pk as $field => $value) {
				$this->data[$field] = self::db()->lastInsertId();
			}
		}
	}

	protected function loadData(array $data) {
		foreach ($this->properties as $property) {
			if (array_key_exists($property, $data)) {
				$this->data[$property] = $data[$property];
			} else {
				$this->data[$property] = null;
			}
		}
	}

	protected function init() {}

}