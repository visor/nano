<?php

/**
 * @group library
 */
class SqlExprTest extends PHPUnit_Framework_TestCase {

	public function testSingleOperation() {
		self::assertEquals("(`a` = 'b')", sql::expr()->add('a', '=', 'b')->__toString());
		self::assertEquals("(`ab`)", sql::expr()->add('ab')->__toString());
	}

	public function testSimpleAnd() {
		self::assertEquals(
			  "(`a` = 'b' and `c` = 'd')"
			, sql::expr()->add('a', '=', 'b')->addAnd('c', '=', 'd')->__toString()
		);
	}

	public function testSimpleOr() {
		self::assertEquals(
			  "(`a` = 'b' or `c` = 'd')"
			, sql::expr()->add('a', '=', 'b')->addOr('c', '=', 'd')->__toString()
		);
	}

	public function testCustom() {
		self::assertEquals(
			  "(custom1 = custom2)"
			, sql::expr()->add(sql::custom('custom1'), '=', sql::custom('custom2'))->__toString()
		);
	}

	public function testExpr() {
		self::assertEquals(
			  "((`a` = 'b'))"
			, sql::expr()->add(sql::expr()->add('a', '=', 'b'))->toString(Nano::db())
		);
	}

	public function testNestedExpr() {
		$expected = "((`a` = 'b' and `c` = 'd') or (`e` = 'f' and `g` = 'h'))";
		$expr     = sql::expr()
			->add(
				sql::expr()->add('a', '=', 'b')->addAnd('c', '=', 'd')
			)->addOr(
				sql::expr()->add('e', '=', 'f')->addAnd('g', '=', 'h')
			);
		self::assertEquals($expected, $expr->toString(Nano::db()));
	}

	public function testDSL() {
		$expected = "(((`a` = 'b' and `c` = 'd') or (`e` = 'f' and `g` = 'h')) and `active` = '1')";
		$actual   = sql::expr()
			->begin()
				->begin()
					->add('a', '=', 'b')
					->and('c', '=', 'd')
				->end
				->beginOr()
					->add('e', '=', 'f')
					->and('g', '=', 'h')
				->end
			->end
			->and('active', '=', 1)
		->__toString();
		self::assertEquals($expected, $actual);
	}

}