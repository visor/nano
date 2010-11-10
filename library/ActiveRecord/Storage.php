<?php

class ActiveRecord_Storage {

	const RELATION_SEPARATOR = '::';

	private static $relations = array();

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

	/**
	 * @return ActiveRecord
	 * @param ActiveRecord $record
	 * @param string $relation
	 */
	public static function getRelatedRecord(ActiveRecord $record, $relationName) {
		$relation = $record->getRelation($relationName);
		if (null === $relation) {
			throw new ActiveRecord_Exception_UnknownRelation($relationName, $record);
		}
		$value = $record->__get($relation[ActiveRecord::REL_FIELD]);
		$class = $relation[ActiveRecord::REL_CLASS];
		$field = $relation[ActiveRecord::REL_REF];
		$key   = $class . '-' . $field . '-' . $value;
		if (null === $value) {
			return $class::instance();
		}
		if (!isset(self::$relations[$key])) {
			self::$relations[$key] = $class::prototype()->findOne(array($field => $value));
			if (!self::$relations[$key]) {
				unset(self::$relations[$key]);
				throw new ActiveRecord_Exception_RelationTargetNotFound($relation, $record);
			}
		}
		return self::$relations[$key];
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