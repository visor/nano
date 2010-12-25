<?php

class Nano_Db_Statement extends PDOStatement {

	/**
	 * @param array $parameters
	 * @return void
	 */
	public function execute($parameters = null) {
		if (Nano::db()->log()->enabled()) {
			$exception = null;
			$now = microtime(true);
			try {
				$result = call_user_func_array(array($this, 'parent::execute'), func_get_args());
			} catch (Exception $e) {
				$exception = $e;
			}
			Nano::db()->log()->append($this->queryString, microTime(true) - $now);
			if ($exception) {
				if (Nano::db()->log()->enabled()) {
					Nano::db()->log()->append($exception->__toString(), null, true);
				}
				throw $e;
			}
			return $result;
		}
		return call_user_func_array(array($this, 'parent::execute'), func_get_args());
	}

}