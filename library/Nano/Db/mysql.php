<?php

class Nano_Db_mysql {

	/**
	 * @return voud
	 * @param Nano_Db $db
	 */
	public static function clean(Nano_Db $db) {
		$tables = $db->query('show tables', PDO::FETCH_NUM);
		foreach ($tables as $row) {
			if (Nano_Migrate::VERSION_TABLE === $row[0]) {
				continue;
			}
			$db->query('truncate table `' . $row[0] . '`');
		}
	}

}