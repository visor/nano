<?php

class Date extends DateTime {

	const FORMAT_MYSQL = 'Y-m-d H:i:s';
	const FORMAT_SQLITE = 'U';

	const ONE_DAY       = 86400; /* 24*60*60 */
	const ONE_MONTH     = 2592000; /* 30*24*60*60 */

	/**
	 * @return Date
	 * @param string $string
	 */
	public static function create($string = null) {
		if ($string instanceof DateTime) {
			return new self($string->format(DateTime::ISO8601));
		}
		if (is_numeric($string)) {
			return new self(date(DateTime::ISO8601, $string));
		}
		return new self($string);
	}

	/**
	 * @return Date
	 */
	public function midnight() {
		$this->setTime(0, 0, 0);
		return $this;
	}

	/**
	 * @return int
	 * @param string|Date|DateTime $date
	 */
	public function daysTo($date) {
		$from = Date::create($this);
		$to   = Date::create($date);

		$from->setTime(0, 0, 0);
		$to->setTime(0, 0, 0);

		$diff = abs($to->format('U') - $from->format('U'));

		if (0 === $diff) {
			return 1;
		}

		$diff = round($diff / self::ONE_DAY);

		return $diff;
	}

	/**
	 * @return string
	 * @param $type
	 */
	public function toSql($type = null) {
		if (null === $type) {
			$type = Nano::db()->getType();
		}
		$format = 'Date::FORMAT_' . strToUpper($type);
		return $this->format(constant($format));
	}

}