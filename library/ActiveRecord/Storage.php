<?php

class ActiveRecord_Storage {

	/**
	 * @return mixed
	 * @param ActiveRecord $record
	 * @param string|sql_select $query
	 */
	public static function load(ActiveRecord $record, $query, $single = false) {
		$className = get_class($record);
		$sqlQuery  = $query instanceof sql_select ? $query->toString(Nano::db()) : $query;
		if (true === $single) {
			return self::loadSingleRecord($sqlQuery, $className);
		}
		return self::loadRecordSet($sqlQuery, $className);
	}

	/**
	 * @return sql_select
	 * @param ActiveRecord $record
	 */
	public static function getSelectQuery(ActiveRecord $record) {
		$table  = Nano::db()->quoteName($record->getTableName());
		return sql::select($table. '.' . sql::ALL)->from($table);
	}

	/**
	 * @static
	 * @param  $tableName
	 * @param  $fields
	 * @param  $alias
	 * @return string
	 */
	protected static function buildSelectFields($tableName, $fields, $alias) {
		$result = array();
		foreach ($fields as $field) {
			$result[Nano::db()->quote($alias . self::RELATION_SEPARATOR . $field)] = $tableName . '.' . $field;
		}
		return $result;
	}

	/**
	 * @return ActiveRecord
	 * @param string $query
	 * @param string $className
	 */
	protected static function loadSingleRecord($query, $className) {
		return Nano::db()->query($query, PDO::FETCH_CLASS, $className, array(null, true))->fetch();
	}

	/**
	 * @return ActiveRecord
	 * @param string $query
	 * @param string $className
	 */
	protected static function loadRecordSet($query, $className) {
		return Nano::db()->query($query, PDO::FETCH_CLASS, $className, array(null, true))->fetchAll();
	}

}