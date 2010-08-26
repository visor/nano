<?php

class TestEditable extends Nano_DbObject {

	const NAME            = 'phpunit_test_editable';

	protected $table      = self::NAME;

	protected $properties = array('id', 'title');

	public static function createTable() {
		self::db()->query(
			'create table `' . self::NAME . '` ('
				. '`id` bigint(11) not null auto_increment'
				. ', `title` varchar(255) not null'
				. ', primary key (`id`)'
			. ') engine=InnoDB default charset=utf8'
		);
	}

	public static function dropTable() {
		self::db()->query('drop table if exists `' . self::NAME . '`');
	}

	/**
	 * @return TestEditable
	 * @param string $title
	 */
	public static function createNew($title) {
		return parent::create(__CLASS__, array('title' => $title));
	}

}