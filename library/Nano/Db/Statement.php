<?php

class Nano_Db_Statement extends PDOStatement {

	/**
	 * @param array $parameters
	 * @return void
	 */
	public function execute($parameters = null) {
		$exception = null;
		if (Nano::db()->log()->enabled()) {
			$now = microtime(true);
		}
		try {
			$result = call_user_func(array($this, 'parent::execute'), $parameters);
		} catch (Exception $e) {
			$exception = $e;
		}
		if (Nano::db()->log()->enabled()) {
			Nano::db()->log()->append($this->queryString, microtime(true) - $now);
		}
		if ($exception) {
			if (Nano::db()->log()->enabled()) {
				Nano::db()->log()->append($exception->__toString(), 'ERROR');
			}
			throw $e;
		}
		return $result;
	}

}