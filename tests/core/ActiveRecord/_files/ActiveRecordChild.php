<?php

/**
 * @property int $id
 * @property int $parent_id
 * @property string $text
 *
 * @property ActiveRecordBasic $parent
 */
class ActiveRecordChild extends ActiveRecordBasic {

	const TABLE_NAME         = 'active_record_child';

	protected $primaryKey    = array('id');
	protected $autoIncrement = true;
	protected $fields        = array('id', 'parent_id', 'text');
	protected $relations     = array(
		'parent' => array(
			  self::REL_CLASS => 'ActiveRecordBasic'
			, self::REL_TYPE  => self::ONE
			, self::REL_FIELD => 'parent_id'
			, self::REL_REF   => 'id'
		)
	);

	public static function createTable() {
		Nano::db()->exec(
			'create table ' . self::TABLE_NAME . '(id int(11) not null auto_increment primary key, parent_id int(11) not null, text varchar(100)) engine=innodb AUTO_INCREMENT=1'
		);
	}

	public static function deleteTable() {
		Nano::db()->query('drop table if exists `' . self::TABLE_NAME . '`');
	}

}