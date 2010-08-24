<?php

class Nano_Db extends PDO {

	const DEFAULT_NAME      = 'default';
	const DEFAULT_TEST_NAME = 'test';

	private static $default = self::DEFAULT_NAME;

	/**
	 * @var Nano_Db[string]
	 */
	private static $instances = array();

	/**
	 * @return Nano_Db
	 * @param string $name
	 */
	public static function instance($name = null) {
		if (null === $name) {
			$name = self::getDefault();
		}
		if (!array_key_exists($name, self::$instances)) {
			$config = self::getConfig($name);
			$dns    = $config['type'] . ':' . $config['dsn'];
			self::$instances[$name] = new self($dns, $config['username'], $config['password'], $config['options']);
			self::$instances[$name]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			self::$instances[$name]->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
		}
		return self::$instances[$name];
	}

	public static function getDefault() {
		if (Nano::isTesting()) {
			return self::DEFAULT_TEST_NAME;
		}
		return self::$default;
	}

	public static function setDefault($name) {
		self::$default = $name;
	}

	public static function clean() {
		$class = self::getTypeClass();
		$class::clean(self::instance());
	}

	public static function close($name = null) {
		if (null === $name) {
			$name = self::getDefault();
		}
		if (array_key_exists($name, self::$instances)) {
			self::$instances[$name] = null;
			unset(self::$instances[$name]);
		}
	}

	protected static function getConfig($name) {
		$config = Nano::config('db');
		if (!array_key_exists($name, $config)) {
			throw new RuntimeException('Unknow database ' . $name);
		}
		return $config[$name];
	}

	public function getType() {
		return $this->getAttribute(PDO::ATTR_DRIVER_NAME);
	}

	/**
	 * @return sql_select
	 */
	public function select() {
		return sql::select();
	}

	public function fetchClass($query, $className, $arguments) {
		return $this->query($query, PDO::FETCH_CLASS, $className, $arguments);
	}

	public function getRow($query, $fetchMode = PDO::FETCH_OBJ) {
		return $this->query($query)->fetch($fetchMode);
	}

	public function getCol($query) {
		$result = array();
		$rows   = $this->query($query, PDO::FETCH_NUM);
		foreach ($rows as $row) {
			$result[] = $row[0];
		}
		return $result;
	}

	public function getAll($query, $fetchMode = PDO::FETCH_OBJ) {
		return $this->query($query)->fetchAll($fetchMode);
	}

	public function getCell($query) {
		Nano_Log::message($query);
		$row = $this->query($query)->fetch(PDO::FETCH_NUM);
		Nano_Log::message(var_export($row, true));
		return $row[0];
	}

	/**
	 * @return array
	 * @param string $query
	 */
	public function getAssoc($query) {
		$rows = $this->query($query, PDO::FETCH_NUM);
		$result = array();
		foreach ($rows as $row) {
			$result[$row[0]] = $row[1];
		}
		return $result;
	}

	public function insert($table, array $values) {
		$sqlFields = array();
		$sqlValues = array();
		foreach ($values as $field => $value) {
			$sqlFields[] = $this->quoteName($field);
			$sqlValues[] = null === $value ? 'null' : $this->quote($value);
		}
		$query = 'insert into ' . $table . '(' . implode(', ', $sqlFields) . ') values (' . implode(', ', $sqlValues) . ')';
		return $this->exec($query);
	}

	public function update($table, array $values, $where) {
		$sqlValues = array();
		foreach ($values as $field => $value) {
			$sqlValues[] = $this->quoteName($field) . ' = ' . (null === $value ? 'null' : $this->quote($value));
		}
		$query = 'update ' . $table . ' set ' . implode(', ', $sqlValues) . ' where ' . $this->buildWhere($where);
		return $this->exec($query);
	}

	public function delete($table, $where = array()) {
		$where = $this->buildWhere($where);
		$query = 'delete from ' . $table . (empty($where) ? '' : ' where ' . $where);
		return $this->exec($query);
	}

	/**
	 * @return string
	 * @param string $string
	 */
	public function quoteName($string) {
		$class = self::getTypeClass();
		return $class::quoteName($string);
	}

	public function buildWhere($where) {
		if (is_string($where)) {
			return $where;
		}

		$result = array();
		foreach ($where as $column => $value) {
			if (null === $value) {
				$result[] = $this->quoteName($column) . ' is null';
			} else {
				$result[] = $this->quoteName($column) . ' = ' . $this->quote($value);
			}
		}
		return implode(' and ', $result);
	}

	protected static function getTypeClass() {
		$result = 'Nano_Db_' . Nano::db()->getType();
		if (!class_exists($result, false)) {
			require LIB . '/Nano/Db/' . Nano::db()->getType() . '.php';
		}
		return $result;
	}
}