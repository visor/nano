<?php

class sql_expr {

	/**
	 * @var array()
	 */
	protected $parts = array();

	/**
	 * @param sql_expr|sql_custom|string $left
	 * @param string $operation
	 * @param sql_expr|sql_custom|string $right
	 */
	public function __construct($left = null, $operation = null, $right = null) {
		if (null !== $left) {
			$this->add($left, $operation, $right);
		}
	}

	/**
	 * @return sql_expr
	 * @param sql_expr|sql_custom|string $left
	 * @param string $operation
	 * @param sql_expr|sql_custom|string $right
	 */
	public function add($left, $operation = null, $right = null) {
		return $this->addPart(sql::SQL_NONE, $left, $operation, $right);
	}

	/**
	 * @return sql_expr
	 * @param sql_expr|sql_custom|string $left
	 * @param string $operation
	 * @param sql_expr|sql_custom|string $right
	 */
	public function addAnd($left, $operation = null, $right = null) {
		return $this->addPart(sql::SQL_AND, $left, $operation, $right);
	}

	/**
	 * @return sql_expr
	 * @param sql_expr|sql_custom|string $left
	 * @param string $operation
	 * @param sql_expr|sql_custom|string $right
	 */
	public function addOr($left, $operation = null, $right = null) {
		return $this->addPart(sql::SQL_OR, $left, $operation, $right);
	}

	/**
	 * @return boolean
	 */
	public function isEmpty() {
		return 0 == count($this->parts);
	}

	/**
	 * @return string
	 * @param Nano_Db $db
	 */
	public function toString(Nano_Db $db = null) {
		if (null === $db) {
			$db = Nano::db();
		}
		$result = '';
		foreach ($this->parts as $part) {
			if ($part['type'] !== sql::SQL_NONE) {
				$result .= ' ' . $part['type'] . ' ';
			}
			$result .= '(';
			if (null !== $part['operation'] && null !== $part['right']) {
				$result .= $this->operand($db, $part['left'], true) . ' ' . $part['operation'] . ' ' . $this->operand($db, $part['right'], false);
			} elseif (null !== $part['operation']) {
				$result .= $this->operand($db, $part['left'], true) . ' ' . $part['operation'];
			} else {
				$result .= $this->operand($db, $part['left'], true);
			}
			$result .= ')';
		}
		return $result;
	}

	public function __call($name, $arguments) {
		if (sql::SQL_OR == $name) {
			return call_user_func_array(array($this, 'addOr'), $arguments);
		}
		if (sql::SQL_AND == $name) {
			return call_user_func_array(array($this, 'addAnd'), $arguments);
		}
	}

	/**
	 * @return sql_expr
	 * @param sql_expr|sql_custom|string $left
	 * @param string $operation
	 * @param sql_expr|sql_custom|string $right
	 */
	protected function addPart($type, $left, $operation = null, $right = null) {
		$this->parts[] = array(
			  'type'      => $type
			, 'left'      => $left
			, 'operation' => $operation
			, 'right'     => $right
		);
		return $this;
	}

	/**
	 * @return string
	 * @param Nano_Db $db
	 * @param sql_expr|sql_custom|string $value
	 * @param bool $field
	 */
	protected function operand(Nano_Db $db, $value, $field) {
		if ($value instanceof sql_custom) { /* @var $value sql_custom */
			return $value->__toString();
		}
		if ($value instanceof sql_expr) { /* @var $value sql_expr */
			return $value->toString($db);
		}
		if ($field) {
			return $value;
		}
		return $db->quote($value);
	}

}