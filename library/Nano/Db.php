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
	 * @var Nano_Db_Log
	 */
	private static $log = null;

	/**
	 * @var string
	 */
	protected $name = null;

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
			self::$instances[$name] = new self($dns, $config['username'], $config['password'], $config['options'], $name);
			self::$instances[$name]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			self::$instances[$name]->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
			self::$instances[$name]->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('Nano_Db_Statement'));
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

	public static function getConfig($name) {
		$config = Nano::config('db');
		if (!array_key_exists($name, $config)) {
			throw new RuntimeException('Unknow database ' . $name);
		}
		return $config[$name];
	}

	/**
	 * @param string $dsn
	 * @param string $username
	 * @param string $password
	 * @param array $options [optional]
	 */
	public function __construct($dsn, $username = null, $password = null, $options = null, $name = null) {
		parent::__construct($dsn, $username, $password, $options);
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return string
	 */
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
		$row = $this->query($query)->fetch(PDO::FETCH_NUM);
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
		$query = 'insert into ' . $this->quoteName($table) . '(' . implode(', ', $sqlFields) . ') values (' . implode(', ', $sqlValues) . ')';
		return $this->exec($query);
	}

	public function update($table, array $values, $where) {
		$sqlValues = array();
		foreach ($values as $field => $value) {
			$sqlValues[] = $this->quoteName($field) . ' = ' . (null === $value ? 'null' : $this->quote($value));
		}
		$query = 'update ' . $this->quoteName($table) . ' set ' . implode(', ', $sqlValues) . ' where ' . $this->buildWhere($where);
		return $this->exec($query);
	}

	public function delete($table, $where = array()) {
		$where = $this->buildWhere($where);
		$query = 'delete from ' . $this->quoteName($table) . (empty($where) ? '' : ' where ' . $where);
		return $this->exec($query);
	}

	/**
	 * @return PDOStatement
	 * @param  string $statement
	 */
	public function query($statement) {
		$numArgs   = func_num_args();
		$fetchMode = $numArgs > 1 ? func_get_arg(1) : null;
		$modeParam = $numArgs > 2 ? func_get_arg(2) : null;
		$params    = $numArgs > 3 ? func_get_arg(3) : null;
		if (Nano::db()->log()->enabled()) {
			$now    = microtime(true);
			if ($numArgs > 3) {
				$result = parent::query($statement, $fetchMode, $modeParam, $params);
			} elseif ($numArgs > 2) {
				$result = parent::query($statement, $fetchMode, $modeParam);
			} elseif ($numArgs > 1) {
				$result = parent::query($statement, $fetchMode);
			} else {
				$result = parent::query($statement);
			}
			$time   = microtime(true) - $now;
			Nano::db()->log()->append($statement, $time);
			return $result;
		}
		if ($numArgs > 3) {
			return parent::query($statement, $fetchMode, $modeParam, $params);
		}
		if ($numArgs > 2) {
			return parent::query($statement, $fetchMode, $modeParam);
		}
		if ($numArgs > 1) {
			return parent::query($statement, $fetchMode);
		}
		return parent::query($statement);
	}

	public function exec($statement) {
		if (Nano::db()->log()->enabled()) {
			$now    = microtime(true);
			$result = parent::exec($statement);
			$time   = microtime(true) - $now;
			Nano::db()->log()->append($statement, $time);
			return $result;
		}
		return parent::exec($statement);
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

	/**
	 * @return Nano_Db_Log
	 */
	public function log() {
		if (null === self::$log) {
			self::$log = new Nano_Db_Log();
		}
		return self::$log;
	}

	protected static function getTypeClass() {
		$result = 'Nano_Db_' . Nano::db()->getType();
		if (!class_exists($result, false)) {
			require LIB . '/Nano/Db/' . Nano::db()->getType() . '.php';
		}
		return $result;
	}
}