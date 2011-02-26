<?php

class ActiveRecord_Storage {

	const CACHE_PREFIX = 'active-record-';

	/**
	 * @return boolean
	 */
	public static function cacheEnabled() {
		if (!isSet(Nano::config('cache')->database)) {
			return false;
		}
		return (boolean)Nano::config('cache')->database;
	}

	/**
	 * @return mixed
	 * @param ActiveRecord $record
	 * @param string|sql_select $query
	 */
	public static function load(ActiveRecord $record, $query, $single = false) {
		$className = get_class($record);
		$sqlQuery  = $query instanceof sql_select ? $query->toString(Nano::db()) : $query;
		if (true === $single) {
			return self::loadSingleRecord($sqlQuery, $className, self::getQueryTables($query));
		}
		return self::loadRecordSet($sqlQuery, $className, self::getQueryTables($query));
	}

	/**
	 * @return void
	 * @param ActiveRecord|string $record
	 */
	public static function invalidateCache($record) {
		Cache::clearTag(array(self::getRecordTag($record)));
	}

	/**
	 * @return string
	 * @param ActiveRecord|string $record
	 */
	public static function getRecordTag($record) {
		$result = self::CACHE_PREFIX . ($record instanceof ActiveRecord ? $record->getTableName() : $record);
		$result = str_replace(array('`', '"', '\''), '', $result);
		return $result;
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
	 * @param string[] $tags
	 */
	protected static function loadSingleRecord($query, $className, $tags) {
		if (self::cacheEnabled()) {
			$key    = self::getCacheKey($query);
			$result = Cache::get($key);
			if (null === $result) {
				$result = self::fetch($query, $className)->fetch();
				Cache::set($key, $result, Date::ONE_DAY, $tags);
			}
			return $result;
		}
		return self::fetch($query, $className)->fetch();
	}

	/**
	 * @return ActiveRecord[]
	 * @param string $query
	 * @param string $className
	 * @param string[] $tags
	 */
	protected static function loadRecordSet($query, $className, $tags) {
		if (self::cacheEnabled()) {
			$key    = self::getCacheKey($query);
			$result = Cache::get($key);
			if (null === $result) {
				$result = self::fetch($query, $className)->fetchAll();
				Cache::set($key, $result, Date::ONE_DAY, $tags);
			}
			return $result;
		}
		return self::fetch($query, $className)->fetchAll();
	}

	/**
	 * @return Nano_Db_Statement
	 * @param string $query
	 * @param string $className
	 */
	protected static function fetch($query, $className) {
		return Nano::db()->query($query, PDO::FETCH_CLASS, $className, array(null, true));
	}

	/**
	 * @return string[]
	 * @param string|sql_select $query
	 */
	protected static function getQueryTables($query) {
		if ($query instanceof sql_select) {
			$result = array();
			foreach ($query->getTableNames() as $name) {
				$result[] = self::getRecordTag($name);
			}
			return $result;
		}
		return array();
	}

	protected static function getCacheKey($sql) {
		//
	}

}