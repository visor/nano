<?php

/**
 * @group library
 */
class Library_DateTest extends PHPUnit_Framework_TestCase {

	public function testNowShouldReturnSameInstance() {
		self::assertSame(Date::now(), Date::now());
	}

	public function testAfterInvalidatingNowShouldReturnNewInstance() {
		$now = Date::now();
		Date::invalidateNow();
		self::assertNotSame($now, Date::now());
	}

	public function testCreateFromFormatShouldUseTimeZoneWhenPassed() {
		$name = 'Europe/Moscow';
		$date = Date::createFromFormat('Y-m-d', '2012-01-01', new DateTimeZone($name));
		self::assertEquals($name, $date->getTimezone()->getName());
	}

	public function testToStringReturnDateTimeInIso8601Format() {
		$date = Date::createFromFormat('Y-m-d H:i:s', '2012-01-01 12:00:00', new DateTimeZone('GMT'));
		self::assertEquals('2012-01-01T12:00:00+0000', $date->__toString());
	}

	public function testFromString() {
		$expected = new Date();
		$expected->setDate(2009, 11, 4);
		$expected->setTime(01, 02, 03);

		$this->assertEquals($expected->format('U'), Date::create('2009-11-04 01:02:03')->format('U'));
		$this->assertEquals($expected->format('U'), Date::create('1257274923')->format('U'));
		$this->assertEquals($expected->format('U'), Date::create(Date::create('1257274923'))->format('U'));
		$this->assertEquals($expected->format('U'), Date::create(new DateTime('2009-11-04 01:02:03'))->format('U'));
		$this->assertEquals($expected->format('U'), Date::create(new Date('2009-11-04 01:02:03'))->format('U'));

		$expected->setTime(0, 0, 0);
		$this->assertEquals($expected->format('U'), Date::create('2009-11-04 00:00:00')->format('U'));
		$this->assertEquals($expected->format('U'), Date::create('2009-11-04')->format('U'));
		$this->assertEquals($expected->format('U'), Date::create('1257271200')->format('U'));
		$this->assertEquals($expected->format('U'), Date::create(Date::create('1257271200'))->format('U'));
		$this->assertEquals($expected->format('U'), Date::create(new DateTime('2009-11-04'))->format('U'));
		$this->assertEquals($expected->format('U'), Date::create(new Date('2009-11-04'))->format('U'));
	}

	public function testToSqlForMysql() {
		$string = '2009-11-04 01:02:03';
		$date   = Date::create($string);

		$this->assertEquals($string, $date->format(Date::FORMAT_MYSQL));
		$this->assertEquals($string, $date->format(Date::FORMAT_MYSQL));
	}

	public function testToSqlForSqlite() {
		$string   = '2009-11-04 01:02:03';
		$date     = Date::create($string);
		$expected = '1257274923';

		$this->assertEquals($expected, $date->format(Date::FORMAT_SQLITE));
	}

	public function testGettingDiffDays() {
		$this->assertEquals(1, Date::create('2009-01-01')->daysTo('2009-01-01'));
		$this->assertEquals(1, Date::create('2009-01-01 00:00:00')->daysTo('2009-01-01 23:59:59'));
		$this->assertEquals(1, Date::create('2009-01-01 23:59:59')->daysTo('2009-01-01 23:59:59'));

		$this->assertEquals(1, Date::create('2009-01-01')->daysTo('2009-01-02'));
		$this->assertEquals(1, Date::create('2009-01-01 00:00:00')->daysTo('2009-01-02 23:59:59'));
		$this->assertEquals(1, Date::create('2009-01-01 23:59:59')->daysTo('2009-01-02 23:59:59'));

		$date = Date::create('2009-01-01');
		$test = Date::create('2009-01-01');
		for ($i = 1; $i <= 365; $i++) {
			$test->modify('+1 day');
			$this->assertEquals($i, $date->daysTo($test), $test->format(Date::FORMAT_MYSQL) . ' ' . $date->format(Date::FORMAT_MYSQL));
		}
	}

	public function testIntervalTest() {
		self::assertEquals('11 декабря 2001 года',                          Date::interval('2001-12-11', '2001-12-11', ' ', ' '));
		self::assertEquals('с 10 по 11 декабря 2001 года',                  Date::interval('2001-12-10', '2001-12-11', ' ', ' '));
		self::assertEquals('с 11 января по 11 декабря 2001 года',           Date::interval('2001-01-11', '2001-12-11', ' ', ' '));
		self::assertEquals('с 11 января 2000 года по 11 декабря 2001 года', Date::interval('2000-01-11', '2001-12-11', ' ', ' '));
	}

}