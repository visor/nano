<?php

class SqlExprTest extends PHPUnit_Framework_TestCase {

	public function testSingleOperation() {
		self::assertEquals("(a = 'b')", sql::expr('a', '=', 'b')->toString(Nano::db()));
		self::assertEquals("(ab)", sql::expr('ab')->toString(Nano::db()));
	}

	public function testSimpleAnd() {
		self::assertEquals(
			  "(a = 'b') and (c = 'd')"
			, sql::expr('a', '=', 'b')->addAnd('c', '=', 'd')->toString(Nano::db())
		);
	}

	public function testSimpleOr() {
		self::assertEquals(
			  "(a = 'b') or (c = 'd')"
			, sql::expr('a', '=', 'b')->addOr('c', '=', 'd')->toString(Nano::db())
		);
	}

	public function testCustom() {
		self::assertEquals(
			  "(custom1 = custom2)"
			, sql::expr(sql::custom('custom1'), '=', sql::custom('custom2'))->toString(Nano::db())
		);
	}

	public function testExpr() {
		self::assertEquals(
			  "((a = 'b'))"
			, sql::expr(sql::expr('a', '=', 'b'))->toString(Nano::db())
		);
	}

	public function testNestedExpr() {
		$expected = "((a = 'b') and (c = 'd')) or ((e = 'f') and (g = 'h'))";
		$expr     = sql::expr(sql::expr('a', '=', 'b')->addAnd('c', '=', 'd'))->addOr(sql::expr('e', '=', 'f')->addAnd('g', '=', 'h'));
		self::assertEquals($expected, $expr->toString(Nano::db()));
	}

}