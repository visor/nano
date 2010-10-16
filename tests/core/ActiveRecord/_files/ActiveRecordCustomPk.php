<?php

/**
 * @property int $id1
 * @property int $id2
 * @property string $text
 */
class ActiveRecordCustomPk extends ActiveRecordBasic {

	const TABLE_NAME      = 'test';

	protected $primaryKey    = array('id1', 'id2');
	protected $autoIncrement = false;
	protected $fields        = array('id1', 'id2', 'text');

	public static function createTable() {
		Nano::db()->query(
			''
		);
	}

	public static function deleteTable() {
		Nano::db()->query('drop table if exists `' . self::TABLE_NAME . '`');
	}

}