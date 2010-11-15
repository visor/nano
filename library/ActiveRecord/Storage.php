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
		return $db->query($query->toString($db), PDO::FETCH_CLASS, $className, array(null, true));
	}

	public static function getSelectQuery(ActiveRecord $record) {
		$table  = Nano::db()->quoteName($record->getTableName());
		$result = sql::select($table. '.' . sql::ALL)->from($table);
//		foreach ($record->getOneRelations() as $name => $relation) {
//			extract($relation);
//			/**
//			 * @var string $class
//			 * @var string $type
//			 * @var array $expr
//			 * @var string $field
//			 * @var string $ref
//			 */
//			$parentTable = Nano::db()->quoteName(constant($class . '::TABLE_NAME'));
//			$condition   = $table . '.' . $field . ' = ' . $parentTable . '.' . $ref;
//			$columns     = self::buildSelectFields($parentTable, $class::prototype()->getFields(), $name);
//			$result->leftJoin($parentTable, $condition, $columns);
//		}
		return $result;
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

}