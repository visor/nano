<?php

class Nano_Db_Statement extends PDOStatement {

	/**
	 * @param array $parameters
	 * @return void
	 */
	public function execute($parameters = null) {
		if (Nano::db()->log()->enabled()) {
			$now    = microtime(true);
			$result = parent::execute($parameters);
			$time   = microtime(true) - $now;
			Nano::db()->log()->append($this->queryString, $time);
			return $result;
		}
		return parent::execute($parameters);
	}

}