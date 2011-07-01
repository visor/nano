<?php

/**
 * @group framework
 * @group routes
 */
class Core_Route_SubdomainTest extends TestUtils_TestCase {

	public function testMatchSubdomainOnly() {
		$_SERVER['HTTP_HOST'] = Nano::config('web')->domain;
		$route = new Nano_Route_Subdomain('.+', '.*');
		self::assertFalse($route->match('some-url'));

		$_SERVER['HTTP_HOST'] = 'some.' . Nano::config('web')->domain;
		self::assertTrue($route->match('some-url'));

		$route = new Nano_Route_Subdomain('some', '.*');
		self::assertTrue($route->match('some-url'));

		$_SERVER['HTTP_HOST'] = 'some2.' . Nano::config('web')->domain;
		self::assertFalse($route->match('some-url'));
	}

	public function testMatchSubdomainAndUrl() {
		$_SERVER['HTTP_HOST'] = Nano::config('web')->domain;
		$route = new Nano_Route_Subdomain('some', 'some');
		self::assertFalse($route->match('some'));

		$_SERVER['HTTP_HOST'] = 'some.' . Nano::config('web')->domain;
		self::assertTrue($route->match('some'));
		self::assertFalse($route->match('some-url'));

		$_SERVER['HTTP_HOST'] = 'some2.' . Nano::config('web')->domain;
		self::assertFalse($route->match('some'));
	}

	public function testParameters() {
		$route = new Nano_Route_Subdomain('(?P<p1>some)', '(?P<p2>some)');
		$_SERVER['HTTP_HOST'] = 'some.' . Nano::config('web')->domain;
		self::assertTrue($route->match('some'));
		self::assertArrayHasKey('p1', $route->matches());
		self::assertArrayHasKey('p2', $route->matches());
	}

}