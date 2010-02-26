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
			  id smallint primary key
			, version text
		)');
	}

	/**
	 * @return void
	 * @param Nano_Db $db
	 */
	public static function sqlite(Nano_Db $db) {
		$db->exec('create table if not exists ' . Nano_Migrate::VERSION_TABLE . '(
			  id integer primary key
			, version text
		)');
	}

}