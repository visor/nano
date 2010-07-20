<?php

class sql {

	const SQL_AND  = 'and';
	const SQL_OR   = 'or';
	const SQL_NONE = null;

	const ALL = '*';

	/**
	 * @return sql_select
	 * @param string|array $columns
	 */
	public static function select($columns = array()) {
		return new sql_select($columns);
	}

	/**
	 * @return sql_custom
	 * @param string $value
	 */
	public static function custom($value) {
		return new sql_custom($value);
	}

	/**
	 * @return sql_expr
	 * @param sql_expr|sql_custom|string $left
	 * @param string $operation
	 * @param sql_expr|sql_custom|string $right
	 */
	public static function expr($left, $operation = null, $right = null) {
		return new sql_expr($left, $operation, $right);
	}

}