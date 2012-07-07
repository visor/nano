<?php

/**
 * @group library
 */
class Library_DateTest extends PHPUnit_Framework_TestCase {

	public function testNowShouldReturnSameInstance() {
		self::assertSame(Nano\Util\Date::now(), Nano\Util\Date::now());
	}

	public function testAfterInvalidatingNowShouldReturnNewInstance() {
		$now = Nano\Util\Date::now();
		Nano\Util\Date::invalidateNow();
		self::assertNotSame($now, Nano\Util\Date::now());
	}

	public function testCreateFromFormatShouldUseTimeZoneWhenPassed() {
		$name = 'Europe/Moscow';
		$date = Nano\Util\Date::createFromFormat('Y-m-d', '2012-01-01', new DateTimeZone($name));
		self::assertEquals($name, $date->getTimezone()->getName());
	}

	public function testToStringReturnDateTimeInIso8601Format() {
		$date = Nano\Util\Date::createFromFormat('Y-m-d H:i:s', '2012-01-01 12:00:00', new DateTimeZone('GMT'));
		self::assertEquals('2012-01-01T12:00:00+0000', $date->__toString());
	}

	public function testFromString() {
		$expected = new Nano\Util\Date();
		$expected->setDate(2009, 11, 4);
		$expected->setTime(01, 02, 03);

		$this->assertEquals($expected->format('U'), Nano\Util\Date::create('2009-11-04 01:02:03')->format('U'));
		$this->assertEquals($expected->format('U'), Nano\Util\Date::create('1257274923')->format('U'));
		$this->assertEquals($expected->format('U'), Nano\Util\Date::create(Nano\Util\Date::create('1257274923'))->format('U'));
		$this->assertEquals($expected->format('U'), Nano\Util\Date::create(new DateTime('2009-11-04 01:02:03'))->format('U'));
		$this->assertEquals($expected->format('U'), Nano\Util\Date::create(new Nano\Util\Date('2009-11-04 01:02:03'))->format('U'));

		$expected->setTime(0, 0, 0);
		$this->assertEquals($expected->format('U'), Nano\Util\Date::create('2009-11-04 00:00:00')->format('U'));
		$this->assertEquals($expected->format('U'), Nano\Util\Date::create('2009-11-04')->format('U'));
		$this->assertEquals($expected->format('U'), Nano\Util\Date::create('1257271200')->format('U'));
		$this->assertEquals($expected->format('U'), Nano\Util\Date::create(Nano\Util\Date::create('1257271200'))->format('U'));
		$this->assertEquals($expected->format('U'), Nano\Util\Date::create(new DateTime('2009-11-04'))->format('U'));
		$this->assertEquals($expected->format('U'), Nano\Util\Date::create(new Nano\Util\Date('2009-11-04'))->format('U'));
	}

	public function testToSqlForMysql() {
		$string = '2009-11-04 01:02:03';
		$date   = Nano\Util\Date::create($string);

		$this->assertEquals($string, $date->format(Nano\Util\Date::FORMAT_MYSQL));
		$this->assertEquals($string, $date->format(Nano\Util\Date::FORMAT_MYSQL));
	}

	public function testToSqlForSqlite() {
		$string   = '2009-11-04 01:02:03';
		$date     = Nano\Util\Date::create($string);
		$expected = '1257274923';

		$this->assertEquals($expected, $date->format(Nano\Util\Date::FORMAT_SQLITE));
	}

	public function testGettingDiffDays() {
		$this->assertEquals(1, Nano\Util\Date::create('2009-01-01')->daysTo('2009-01-01'));
		$this->assertEquals(1, Nano\Util\Date::create('2009-01-01 00:00:00')->daysTo('2009-01-01 23:59:59'));
		$this->assertEquals(1, Nano\Util\Date::create('2009-01-01 23:59:59')->daysTo('2009-01-01 23:59:59'));

		$this->assertEquals(1, Nano\Util\Date::create('2009-01-01')->daysTo('2009-01-02'));
		$this->assertEquals(1, Nano\Util\Date::create('2009-01-01 00:00:00')->daysTo('2009-01-02 23:59:59'));
		$this->assertEquals(1, Nano\Util\Date::create('2009-01-01 23:59:59')->daysTo('2009-01-02 23:59:59'));

		$date = Nano\Util\Date::create('2009-01-01');
		$test = Nano\Util\Date::create('2009-01-01');
		for ($i = 1; $i <= 365; $i++) {
			$test->modify('+1 day');
			$this->assertEquals($i, $date->daysTo($test), $test->format(Nano\Util\Date::FORMAT_MYSQL) . ' ' . $date->format(Nano\Util\Date::FORMAT_MYSQL));
		}
	}

	public function testIntervalTest() {
		self::assertEquals('11 декабря 2001 года',                          Nano\Util\Date::interval('2001-12-11', '2001-12-11', ' ', ' '));
		self::assertEquals('с 10 по 11 декабря 2001 года',                  Nano\Util\Date::interval('2001-12-10', '2001-12-11', ' ', ' '));
		self::assertEquals('с 11 января по 11 декабря 2001 года',           Nano\Util\Date::interval('2001-01-11', '2001-12-11', ' ', ' '));
		self::assertEquals('с 11 января 2000 года по 11 декабря 2001 года', Nano\Util\Date::interval('2000-01-11', '2001-12-11', ' ', ' '));
	}

}