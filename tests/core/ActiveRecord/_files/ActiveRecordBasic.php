<?php

/**
 * @property int $id
 * @property string $text
 */
class ActiveRecordBasic extends ActiveRecord {

	const TABLE_NAME         = 'active_record_test';

	protected $primaryKey    = array('id');
	protected $autoIncrement = true;
	protected $fields        = array('id', 'text');

	public
		  $beforeDelete = null
		, $afterDelete  = null
		, $beforeInsert = null
		, $afterInsert  = null
		, $beforeUpdate = null
		, $afterUpdate  = null
	;

	/**
	 * @return sql_expr
	 * @param mixed $params
	 */
	public function getSelectCriteria($params) {
		return $this->buildSelectCriteria($params);
	}

	/**
	 * @return mixed
	 * @param mixed $params
	 */
	public function getWhereFields($params) {
		return $this->buildWhereFields($params);
	}

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
		Nano::db()->exec(
			'create table ' . self::TABLE_NAME . '(id int(11) not null auto_increment primary key, text varchar(100)) engine=innodb AUTO_INCREMENT=1'
		);
	}

	public static function deleteTable() {
		Nano::db()->query('drop table if exists `' . self::TABLE_NAME . '`');
	}

	/**
	 * @return void
	 */
	protected function beforeDelete() {
		parent::beforeDelete();
		$this->{__FUNCTION__} = true;
	}

	/**
	 * @return void
	 */
	protected function afterDelete() {
		parent::afterDelete();
		$this->{__FUNCTION__} = true;
	}

	/**
	 * @return void
	 */
	protected function beforeInsert() {
		parent::beforeInsert();
		$this->{__FUNCTION__} = true;
	}

	/**
	 * @return void
	 */
	protected function afterInsert() {
		parent::afterInsert();
		$this->{__FUNCTION__} = true;
	}

	/**
	 * @return void
	 */
	protected function beforeUpdate() {
		parent::beforeUpdate();
		$this->{__FUNCTION__} = true;
	}

	/**
	 * @return void
	 */
	protected function afterUpdate() {
		parent::afterUpdate();
		$this->{__FUNCTION__} = true;
	}

}