<?php

class ActiveRecord_Storage {

	/**
	 * @return PDOStatement
	 * @param ActiveRecord $record
	 * @param sql_select $query
	 */
	public static function load(ActiveRecord $record, sql_select $query) {
		$className = get_class($record);
		$db        = Nano::db();
		return $db->query($query->toString($db), PDO::FETCH_CLASS, $className, array());
	}

}