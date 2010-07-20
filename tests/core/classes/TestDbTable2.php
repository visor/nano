<?php

class TestDbTable2 extends Nano_DbObject {

	const NAME            = 'test2';

	protected $table      = self::NAME;

	protected $primaryKey = 'id';

	protected $properties = array(
		  'id'
		, 'field2'
		, 'field3'
		, 'field4'
	);

	public static function createTable() {
		self::db()->exec(
			'create table ' . self::NAME . '('
				. ' id int(11) primary key auto_increment'
				. ', field2 int(11)'
				. ', field3 text'
				. ', field4 text'
			. ');'
		);
	}

	public static function dropTable() {
		self::db()->exec('drop table if exists ' . self::NAME);
	}

}