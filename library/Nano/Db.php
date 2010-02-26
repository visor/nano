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
		$class = 'Nano_Db_' . Nano::db()->getType();
		if (!class_exists($class, false)) {
			require LIB . '/Nano/Db/' . Nano::db()->getType() . '.php';
		}
		call_user_func(array($class, 'clean'), Nano::db());
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

	public function getCell($query) {
		$row = $this->query($query)->fetch(PDO::FETCH_NUM);
		return $row[0];
	}

	public function insert($table, array $values) {
		$sqlFields = array();
		$sqlValues = array();
		foreach ($values as $field => $value) {
			$sqlFields[] = $field;
			$sqlValues[] = null === $value ? 'null' : $this->quote($value);
		}
		$query = 'insert into ' . $table . '(' . implode(', ', $sqlFields) . ') values (' . implode(', ', $sqlValues) . ')';
		return $this->exec($query);
	}

	public function update($table, array $values, $where) {
		$sqlValues = array();
		foreach ($values as $field => $value) {
			$sqlValues[] = $field . ' = ' . (null === $value ? 'null' : $this->quote($value));
		}
		$query = 'update ' . $table . ' set ' . implode(', ', $sqlValues) . ' where ' . $this->buildWhere($where);
		return $this->exec($query);
	}

	public function delete($table, $where = array()) {
		$where = $this->buildWhere($where);
		$query = 'delete from ' . $table . (empty($where) ? '' : ' where ' . $where);
		return $this->exec($query);
	}

	public function buildWhere($where) {
		if (is_string($where)) {
			return $where;
		}

		$result = array();
		foreach ($where as $column => $value) {
			if (null === $value) {
				$result[] = $column . ' is null';
			} else {
				$result[] = $column . ' = ' . $this->quote($value);
			}
		}
		return implode(' and ', $result);
	}

}