<?php

/**
 * @property int $id
 * @property string $text
 */
class ActiveRecordBasic extends ActiveRecord {

	const TABLE_NAME      = 'test';

	protected $primaryKey    = array('id');
	protected $autoIncrement = true;
	protected $fields        = array('id', 'text');

	/**
	 * @return sql_expr
	 */
	public function getDeleteCriteria() {
		return $this->buildDeleteCriteria();
	}

	/**
	 * @return sql_expr
	 */
	public function getUpdateCriteria() {
		return $this->buildUpdateCriteria();
	}

	/**
	 * @return sql_expr
	 */
	public function getUpdateFields() {
		return $this->buildUpdateFields();
	}

	/**
	 * @return array
	 */
	public function getInsertFields() {
		return $this->buildInsertFields();
	}

	public static function createTable() {
		Nano::db()->query(
			''
		);
	}

	public static function deleteTable() {
		Nano::db()->query('drop table if exists `' . self::TABLE_NAME . '`');
	}

}