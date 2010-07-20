<?php

abstract class Nano_DbObject_Undelete extends Nano_DbObject {

	/**
	 * @return sql_select
	 * @param sql_select $query
	 * @param string $alias
	 */
	public static function getDeleted(sql_select $query = null, $alias = null) {
		if (null === $query) {
			$query = self::createQuery(get_called_class(), $alias);
		}
		return $query->where(sql::expr(($alias ? $alias . '.' : '') . 'deleted', '=', 1));
	}

	/**
	 * @return sql_select
	 * @param sql_select $query
	 * @param string $alias
	 */
	public static function excludeDeleted(sql_select $query = null, $alias = null) {
		if (null === $query) {
			$query = self::createQuery(get_called_class(), $alias);
		}
		return $query->where(sql::expr(($alias ? $alias . '.' : '') . 'deleted', '=', 0));
	}

	/**
	 * @return boolean
	 */
	public function isDeleted() {
		return 1 == $this->deleted;
	}

	/**
	 * @return boolean
	 */
	public function delete() {
		try {
			$this->beforeDelete();
			$this->deleted = 1;
			self::db()->update($this->table, array('deleted' => 1), $this->getPrimaryKey());
			$this->afterDelete();
		} catch (Exception $e) {
			ErrorLog::append($e);
			return false;
		}
		return true;
	}

	/**
	 * @return Nano_DbObject
	 */
	public function save() {
		if ($this->isNew() && null === $this->deleted) {
			$this->deleted = 0;
		}
		return parent::save();
	}

	/**
	 * @return boolean
	 */
	public function undelete() {
		try {
			$this->deleted = 0;
			self::db()->update($this->table, array('deleted' => 0), $this->getPrimaryKey());
		} catch (Exception $e) {
			ErrorLog::append($e);
			return false;
		}
		return true;
	}

	/**
	 * @return boolean
	 */
	public function prune() {
		if ($this->isNew()) {
			return false;
		}
		$this->beforePrune();
		$rows = self::db()->delete($this->table, $this->getPrimaryKey());
		if (1 != $rows) {
			return false;
		}
		$this->afterPrune();
		return true;
	}

	protected function beforePrune() {}

	protected function afterPrune() {}

}