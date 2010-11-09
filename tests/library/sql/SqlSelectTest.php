<?php

/**
 * @group framework
 */
class SqlSelectTest extends PHPUnit_Framework_TestCase {

	public function testEmpty() {
		self::assertEquals('select', sql::select()->toString(Nano::db()));
	}

	public function testSmallExpression() {
		self::assertEquals('select 2+2', sql::select('2+2')->toString(Nano::db()));
		self::assertEquals('select 2+2 as sum', sql::select(array('sum' => '2+2'))->toString(Nano::db()));
		self::assertEquals('select *', sql::select(sql::ALL)->toString(Nano::db()));
	}

	public function testSeveralColumns() {
		self::assertEquals('select a, b as field_b, c, d', sql::select(array('a', 'field_b' => 'b', 'c', 'd'))->toString(Nano::db()));
	}

	public function testSeveralTables() {
		self::assertEquals(
			'select * from t1, t2'
			, sql::select(sql::ALL)
				->from('t1')
				->from('t2')
				->toString(Nano::db())
		);
		self::assertEquals(
			'select * from t1 as t, t2'
			, sql::select(sql::ALL)
				->from(array('t' => 't1'))
				->from('t2')
				->toString(Nano::db())
		);
	}

	public function testInnerJoin() {
		self::assertEquals(
			'select * from t1 inner join t2 on (t1.id = t2.id)'
			, sql::select(sql::ALL)
				->from('t1')
				->innerJoin('t2', 't1.id = t2.id')
				->toString(Nano::db())
		);
	}

	public function testLeftJoin() {
		self::assertEquals(
			'select * from t1 left join t2 on (t1.id = t2.id)'
			, sql::select(sql::ALL)
				->from('t1')
				->leftJoin('t2', 't1.id = t2.id')
				->toString(Nano::db())
		);
	}

	public function testWhere() {
		self::assertEquals(
			"select * from t where (a = 'b')"
			, sql::select(sql::ALL)
				->from('t')
				->where(sql::expr()->add('a', '=', 'b'))
				->toString(Nano::db())
		);
		self::assertEquals(
			"select * from t where (a = 'b') and (c = 'd')"
			, sql::select(sql::ALL)
				->from('t')
				->where(sql::expr()->add('a', '=', 'b'))
				->where(sql::expr()->add('c', '=', 'd'))
				->toString(Nano::db())
		);
		self::assertEquals(
			"select * from t where (a = 'b') or (c = 'd')"
			, sql::select(sql::ALL)
				->from('t')
				->where(sql::expr()->add('a', '=', 'b'))
				->orWhere(sql::expr()->add('c', '=', 'd'))
				->toString(Nano::db())
		);
		self::assertEquals(
			"select * from t where custom expr"
			, sql::select(sql::ALL)
				->from('t')
				->where(sql::custom('custom expr'))
				->toString(Nano::db())
		);
		self::assertEquals(
			"select * from t where custom expr"
			, sql::select(sql::ALL)
				->from('t')
				->where('custom expr')
				->toString(Nano::db())
		);
	}

	public function testGroup() {
		self::assertEquals('select * from t group by f1', sql::select(sql::ALL)->from('t')->group('f1')->toString(Nano::db()));
		self::assertEquals(
			'select * from t group by f1 asc, f2 desc'
			, sql::select(sql::ALL)
				->from('t')
				->group('f1 asc')
				->group('f2 desc')
				->toString(Nano::db())
		);
		self::assertEquals(
			'select * from t group by f1 asc, f2 desc'
			, sql::select(sql::ALL)
				->from('t')
				->group(array('f1 asc', 'f2 desc'))
				->toString(Nano::db())
		);
	}

	public function testHaving() {
		self::assertEquals(
			"select * from t having (a = 'b')"
			, sql::select(sql::ALL)
				->from('t')
				->having(sql::expr()->add('a', '=', 'b'))
				->toString(Nano::db())
		);
		self::assertEquals(
			"select * from t having (a = 'b') and (c = 'd')"
			, sql::select(sql::ALL)
				->from('t')
				->having(sql::expr()->add('a', '=', 'b'))
				->having(sql::expr()->add('c', '=', 'd'))
				->toString(Nano::db())
		);
		self::assertEquals(
			"select * from t having (a = 'b') or (c = 'd')"
			, sql::select(sql::ALL)
				->from('t')
				->having(sql::expr()->add('a', '=', 'b'))
				->orHaving(sql::expr()->add('c', '=', 'd'))
				->toString(Nano::db())
		);
		self::assertEquals(
			"select * from t having custom expr"
			, sql::select(sql::ALL)
				->from('t')
				->having(sql::custom('custom expr'))
				->toString(Nano::db())
		);
		self::assertEquals(
			"select * from t having custom expr"
			, sql::select(sql::ALL)
				->from('t')
				->having('custom expr')
				->toString(Nano::db())
		);
	}

	public function testOrder() {
		self::assertEquals('select * from t order by f1', sql::select(sql::ALL)->from('t')->order('f1')->toString(Nano::db()));
		self::assertEquals(
			'select * from t order by f1 asc, f2 desc'
			, sql::select(sql::ALL)
				->from('t')
				->order('f1 asc')
				->order('f2 desc')
				->toString(Nano::db())
		);
		self::assertEquals(
			'select * from t order by f1 asc, f2 desc'
			, sql::select(sql::ALL)
				->from('t')
				->order(array('f1 asc', 'f2 desc'))
				->toString(Nano::db())
		);
	}

	public function testLimit() {
		self::assertEquals('select * from t limit 1', sql::select(sql::ALL)->from('t')->limit(1)->toString(Nano::db()));
		self::assertEquals('select * from t limit 20, 10', sql::select(sql::ALL)->from('t')->limit(10, 20)->toString(Nano::db()));
		self::assertEquals('select * from t limit 0, 5', sql::select(sql::ALL)->from('t')->limitPage(1, 5)->toString(Nano::db()));
		self::assertEquals('select * from t limit 5, 5', sql::select(sql::ALL)->from('t')->limitPage(2, 5)->toString(Nano::db()));
		self::assertEquals('select * from t limit 10, 5', sql::select(sql::ALL)->from('t')->limitPage(3, 5)->toString(Nano::db()));
	}

}