<?php

class sql_select {

	/**
	 * @var boolean
	 */
	protected $distinct = false;

	/**
	 * @var array
	 */
	protected $columns = array();

	/**
	 * @var array
	 */
	protected $from   = array();

	/**
	 * @var array
	 */
	protected $where  = array();

	/**
	 * @var array
	 */
	protected $group  = array();

	/**
	 * @var array
	 */
	protected $having = array();

	/**
	 * @var array
	 */
	protected $order  = array();

	/**
	 * @var int
	 */
	protected $offset = null;

	/**
	 * @var int
	 */
	protected $limit  = null;

	/**
	 * @param string|array $columns
	 */
	public function __construct($columns = array()) {
		$this->columns($columns);
	}

	/**
	 * @return sql_select
	 * @param bool $value
	 */
	public function distinct($value = true) {
		$this->distinct = (bool)$value;
		return $this;
	}

	/**
	 * @return sql_select
	 * @param string|array $columns
	 */
	public function columns($columns = array()) {
		if (is_array($columns)) {
			foreach ($columns as $alias => $field) {
				$this->columns[$alias] = $field;
			}
		} else {
			$this->columns[] = $columns;
		}
		return $this;
	}

	/**
	 * @return sql_select
	 * @param string|array $table
	 * @param string|array $columns
	 */
	public function from($table, $columns = null) {
		$this->join(null, $table, null, $columns);
		return $this;
	}

	/**
	 * @return sql_select
	 */
	public function innerJoin($table, $condition, $columns = null) {
		$this->join('inner', $table, $condition, $columns);
		return $this;
	}

	/**
	 * @return sql_select
	 */
	public function leftJoin($table, $condition, $columns = null) {
		$this->join('left', $table, $condition, $columns);
		return $this;
	}

	/**
	 * @return sql_select
	 * @param sql_expr|sql_custom|string $expr
	 */
	public function where($expr) {
		if (empty($expr)) {
			return $this;
		}
		$this->where[] = array(
			  'type' => sql::SQL_AND
			, 'expr' => $expr
		);
		return $this;
	}

	/**
	 * @return sql_select
	 * @param sql_expr|sql_custom|string $expr
	 */
	public function orWhere($expr) {
		$this->where[] = array(
			  'type' => sql::SQL_OR
			, 'expr' => $expr
		);
		return $this;
	}

	/**
	 * @return sql_select
	 * @param string|array $spec
	 */
	public function group($spec) {
		if (is_array($spec)) {
			foreach ($spec as $part) {
				$this->group[] = $part;
			}
		} else {
			$this->group[] = $spec;
		}
		return $this;
	}

	/**
	 * @return sql_select
	 * @param sql_expr|sql_custom|string $expr
	 */
	public function having($expr) {
		$this->having[] = array(
			  'type' => sql::SQL_AND
			, 'expr' => $expr
		);
		return $this;
	}

	/**
	 * @return sql_select
	 * @param sql_expr|sql_custom|string $expr
	 */
	public function orHaving($expr) {
		$this->having[] = array(
			  'type' => sql::SQL_OR
			, 'expr' => $expr
		);
		return $this;
	}

	/**
	 * @return sql_select
	 * @param string|array $spec
	 */
	public function order($spec) {
		if (is_array($spec)) {
			foreach ($spec as $part) {
				$this->order[] = $part;
			}
		} else {
			$this->order[] = $spec;
		}
		return $this;
	}

	/**
	 * @return sql_select
	 * @param int $count
	 * @param int $offset
	 */
	public function limit($count, $offset = null) {
		if (null !== $offset) {
			$this->offset = (int)$offset;
		}
		$this->limit  = (int)$count;
		return $this;
	}

	/**
	 * @return sql_select
	 * @param int $page
	 * @param int $count
	 */
	public function limitPage($page, $count) {
		if (null === $page) {
			return $this;
		}
		if ($page < 1) {
			$page = 1;
		}
		if ($count < 1) {
			$count = 1;
		}

		$this->limit  = (int)$count;
		$this->offset = $this->limit * ((int)$page - 1);
		return $this;
	}

	/**
	 * @return string[]
	 */
	public function getTableNames() {
		$result = array();
		foreach ($this->from as $from) {
			$result[] = is_array($from['table']) ? current($from['table']) : $from['table'];
		}
		return $result;
	}

	/**
	 * @return PDOStatement
	 * @param Nano_Db $db
	 */
	public function execute(Nano_Db $db = null) {
		if (null === $db) {
			$db = Nano::db();
		}
		return $db->query($this->toString($db));
	}

	/**
	 * @return string
	 */
	public function toString(Nano_Db $db = null) {
		if (null === $db) {
			$db = Nano::db();
		}
		return
			$this->buildSelect()
			. $this->buildFrom()
			. $this->buildWhere($db)
			. $this->buildGroup()
			. $this->buildHaving($db)
			. $this->buildOrder()
			. $this->buildLimit()
		;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->toString();
	}

	protected function join($type, $table, $condition = null, $columns = null) {
		if (null !== $columns) {
			$this->columns($columns);
		}
		$this->from[] = array(
			  'type'      => $type
			, 'table'     => $table
			, 'condition' => $condition
		);
	}

	/**
	 * @return string
	 */
	protected function buildSelect() {
		$result = 'select';
		if ($this->distinct) {
			$result .= ' distinct';
		}
		$first  = true;
		foreach ($this->columns as $alias => $field) {
			if ($first) {
				$first = false;
			} else {
				$result .= ',';
			}
			$result .= ' ' . $field;
			if (is_string($alias)) {
				$result .= ' as ' . $alias;
			}
		}
		return $result;
	}

	/**
	 * @return string
	 */
	protected function buildFrom() {
		if (empty($this->from)) {
			return null;
		}
		$result = ' from ';
		$first  = true;
		foreach ($this->from as $from) {
			if (null === $from['type']) {
				if (!$first) {
					$result .= ', ';
				}
			} else {
				$result .= ' ' . $from['type'] . ' join ';
			}
			if (is_array($from['table'])) {
				$result .= current($from['table']) . ' as '. key($from['table']);
			} else {
				$result .= $from['table'];
			}
			if ($from['condition']) {
				$result .= ' on (' . $from['condition'] . ')';
			}
			if ($first) {
				$first = false;
			}
		}

		return $result;
	}

	/**
	 * @return string
	 */
	protected function buildWhere(Nano_Db $db) {
		if (empty($this->where)) {
			return null;
		}
		$result = ' where ';
		$first  = true;
		foreach ($this->where as $part) {
			if ($first) {
				$first = false;
			} else {
				$result .= ' ' . $part['type'] . ' ';
			}
			if ($part['expr'] instanceof sql_expr) {
				$result .= $part['expr']->toString($db);
			} else {
				$result .= $part['expr'];
			}
		}
		return $result;
	}

	/**
	 * @return string
	 */
	protected function buildGroup() {
		if (empty($this->group)) {
			return null;
		}
		return ' group by ' . implode(', ', $this->group);
	}

	/**
	 * @return string
	 */
	protected function buildHaving(Nano_Db $db) {
		if (empty($this->having)) {
			return null;
		}
		$result = ' having ';
		$first  = true;
		foreach ($this->having as $part) {
			if ($first) {
				$first = false;
			} else {
				$result .= ' ' . $part['type'] . ' ';
			}
			if ($part['expr'] instanceof sql_expr) {
				$result .= $part['expr']->toString($db);
			} else {
				$result .= $part['expr'];
			}
		}
		return $result;
	}

	/**
	 * @return string
	 */
	protected function buildOrder() {
		if (empty($this->order)) {
			return null;
		}
		return ' order by ' . implode(', ', $this->order);
	}

	/**
	 * @return string
	 */
	protected function buildLimit() {
		if (null !== $this->limit && null !== $this->offset) {
			return ' limit ' . $this->offset . ', ' . $this->limit;
		} elseif (null !== $this->limit) {
			return ' limit ' . $this->limit;
		}
		return null;
	}

}