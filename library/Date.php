<?php

class Date extends DateTime {

	const FORMAT_MYSQL  = 'Y-m-d H:i:s';
	const FORMAT_SQLITE = 'U';

	const ONE_DAY       = 86400; /* 24*60*60 */
	const ONE_MONTH     = 2592000; /* 30*24*60*60 */

	const FROM_STRING   = 'с';
	const TO_STRING     = 'по';
	const YEAR_STRING   = 'года';

	/**
	 * @var Date
	 */
	private static $now = null;

	private static $monthes = array(
		'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'
	);

	/**
	 * @return Date
	 * @param string $string
	 */
	public static function create($string = null) {
		if ($string instanceof DateTime) {
			/** @var DateTime $string */
			return new self($string->format('Y-m-d\TH:i:s'), $string->getTimezone());
		}
		if (is_numeric($string)) {
			return Date::createFromFormat('U', (int)$string);
		}
		return new self($string);
	}

	/**
	 * @return Date
	 */
	public static function now() {
		if (null === self::$now) {
			self::$now = Date::create('now');
		}
		return self::$now;
	}

	/**
	 * For test purpose only
	 */
	public static function invalidateNow() {
		self::$now = null;
	}

	/**
	 * @return string
	 * @param string|Date $from
	 * @param string|Date $to
	 * @param string $glue
	 * @param string $separator
	 */
	public static function interval($from, $to, $glue = ' ', $separator = ' ') {
		$fromDate = self::create($from)->midnight();
		$toDate   = self::create($to)->midnight();

		if ($fromDate->getTimestamp() === $toDate->getTimestamp()) {
			return $fromDate->format('j') . $glue . $fromDate->month() . $glue . $fromDate->format('Y') . $glue . self::YEAR_STRING;
		}

		$nowYear    = Date::now()->format('Y');
		$isOneYear  = $fromDate->format('Y') === $toDate->format('Y');
		$isOneMonth = $isOneYear && ($fromDate->format('m') === $toDate->format('m'));
		$toString   = '';
		$fromString = '';

		$toString .= $toDate->format('Y') . $glue . self::YEAR_STRING;
		if (!$isOneYear) {
			$fromString .= $fromDate->format('Y') . $glue . self::YEAR_STRING;
		}

		$toString = $toDate->month() . (empty($toString) ? '' : $glue . $toString);
		if (!$isOneMonth) {
			$fromString = $fromDate->month() . (empty($fromString) ? '' : $glue . $fromString);
		}

		$toString   = $toDate->format('j') . (empty($toString) ? '' : $glue . $toString);
		$fromString = $fromDate->format('j') . (empty($fromString) ? '' : $glue . $fromString);

		$result = self::FROM_STRING . $glue . $fromString . $separator . self::TO_STRING . $glue . $toString;
		$result = str_replace($glue.$glue, $glue, $result);
		return $result;
	}

	/**
	 * @return Date
	 * @param string $format
	 * @param string $time
	 * @param DateTimeZone|null $timeZone
	 */
	public static function createFromFormat($format, $time, $timeZone = null) {
		if (null === $timeZone) {
			$date = parent::createFromFormat($format, $time);
		} else {
			$date = parent::createFromFormat($format, $time, $timeZone);
		}
		return self::create($date);
	}

	/**
	 * @return string
	 */
	public function month() {
		return self::$monthes[$this->format('m') - 1];
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

	public function __toString() {
		return $this->format(Date::ISO8601);
	}

}
