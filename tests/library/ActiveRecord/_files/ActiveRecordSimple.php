<?php

/**
 * @property int $id
 * @property string $text
 *
 * @method ActiveRecordBasic instance()
 * @method ActiveRecordBasic prototype()
 * @method ActiveRecordBasic findOne()
 */
class ActiveRecordSimple extends ActiveRecord {

	const TABLE_NAME         = 'active_record_simple';

	protected $primaryKey    = array('id');
	protected $autoIncrement = true;
	protected $fields        = array('id', 'text');

	public static function createTable() {
		Nano::db()->exec(
			'create table ' . self::TABLE_NAME . '(id int(11) not null auto_increment primary key, text varchar(100)) engine=innodb AUTO_INCREMENT=1'
		);
	}

	public static function deleteTable() {
		Nano::db()->query('drop table if exists `' . self::TABLE_NAME . '`');
	}

}