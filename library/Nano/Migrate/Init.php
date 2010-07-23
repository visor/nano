<?php

class Nano_Migrate_Init {

	/**
	 * @return void
	 * @param Nano_Db $db
	 */
	public static function init(Nano_Db $db) {
		$method = $db->getType();
		self::$method($db);
	}

	/**
	 * @return void
	 * @param Nano_Db $db
	 */
	public static function mysql(Nano_Db $db) {
		$db->exec('create table ' . Nano_Migrate::VERSION_TABLE . '(
			  id bigint not null auto_increment primary key
			, version varchar(255) unique

		)');
	}

	/**
	 * @return void
	 * @param Nano_Db $db
	 */
	public static function sqlite(Nano_Db $db) {
		$db->exec('create table ' . Nano_Migrate::VERSION_TABLE . '(
			  id integer not null primary key
			, version text
		)');
	}

}