<?php

/**
 * @property int $id
 * @property int $parent_id1
 * @property int $parent_id2
 * @property string $text
 *
 * @property ActiveRecordBasic $parent1
 * @property ActiveRecordSimple $parent2
 *
 * @method ActiveRecordExtendedChild instance()
 * @method ActiveRecordExtendedChild prototype()
 * @method ActiveRecordExtendedChild findOne()
 * @method ActiveRecordExtendedChild prototype()
 */
class ActiveRecordExtendedChild extends ActiveRecordBasic {

	const TABLE_NAME         = 'active_record_ext_child';

	protected $primaryKey    = array('id');
	protected $autoIncrement = true;
	protected $fields        = array('id', 'parent_id1', 'parent_id2', 'text');
	protected $relations     = array(
		'parent1' => array(
			  self::REL_CLASS => 'ActiveRecordBasic'
			, self::REL_TYPE  => self::ONE
			, self::REL_FIELD => 'parent_id1'
			, self::REL_REF   => 'id'
		)
		, 'parent2' => array(
			  self::REL_CLASS => 'ActiveRecordSimple'
			, self::REL_TYPE  => self::ONE
			, self::REL_FIELD => 'parent_id2'
			, self::REL_REF   => 'id'
		)
	);

	public static function createTable() {
		Nano::db()->exec(
			'create table ' . self::TABLE_NAME . '(id int(11) not null auto_increment primary key, parent_id1 int(11) not null, parent_id2 int(11) not null, text varchar(100)) engine=innodb AUTO_INCREMENT=1'
		);
	}

	public static function deleteTable() {
		Nano::db()->query('drop table if exists `' . self::TABLE_NAME . '`');
	}

}