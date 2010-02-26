<?php

class TestDbTable1 extends Nano_DbObject {

	const NAME            = 'test1';

	protected $table      = self::NAME;

	protected $primaryKey = array(
		  'field1'
		, 'field2'
	);

	protected $increment  = false;

	protected $properties = array(
		  'field1'
		, 'field2'
		, 'field3'
		, 'field4'
	);

	/**
	 * @return TestNano_DbTable1
	 * @param int $field1
	 * @param int $field2
	 */
	public static function createNew($field1, $field2) {
		$data = array(
			  'field1' => $field1
			, 'field2' => $field2
		);
		return parent::createNew(__CLASS__, $data);
	}

	public static function createTable() {
		self::db()->exec(
			'create table ' . self::NAME . '('
				. ' field1 int(11)'
				. ', field2 int(11)'
				. ', field3 text'
				. ', field4 text'
				. ', primary key(field1, field2)'
			. ');'
		);
	}

	public static function dropTable() {
		self::db()->exec('drop table if exists ' . self::NAME);
	}
}