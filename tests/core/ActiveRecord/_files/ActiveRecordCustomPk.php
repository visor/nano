<?php

/**
 * @property int $id1
 * @property int $id2
 * @property string $text
 */
class ActiveRecordCustomPk extends ActiveRecordBasic {

	const TABLE_NAME         = 'active_record_test_pk';

	protected $primaryKey    = array('id1', 'id2');
	protected $autoIncrement = false;
	protected $fields        = array('id1', 'id2', 'text');

	public static function createTable() {
		Nano::db()->exec(
			'create table ' . self::TABLE_NAME . '(id1 int(11) not null, id2 int(11) not null, text varchar(100), primary key (id1, id2)) engine=innodb AUTO_INCREMENT=1'
		);
	}

	public static function deleteTable() {
		Nano::db()->query('drop table if exists `' . self::TABLE_NAME . '`');
	}

}