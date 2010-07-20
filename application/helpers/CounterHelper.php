<?php

class CounterHelper extends Nano_Helper {

	private static $counters = array();

	/**
	 * @return int
	 * @param string $name
	 */
	public function invoke($name = null) {
		if (!array_key_exists($name, self::$counters)) {
			self::$counters[$name] = 0;
		}
		++self::$counters[$name];

		return self::$counters[$name];
	}

}