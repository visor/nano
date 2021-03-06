<?php

/**
 * @group core
 * @group routes
 */
class Core_Route_SubdomainTest extends \Nano\TestUtils\TestCase {

	public function testMatchSubdomainOnly() {
		$route = new \Nano\Route\Subdomain('.+', null);

		$_SERVER['HTTP_HOST'] = Nano::app()->config->get('web')->domain;
		self::assertFalse($route->match('some-url'));

		$_SERVER['HTTP_HOST'] = 'some.' . Nano::app()->config->get('web')->domain;
		self::assertTrue($route->match('some-url'));

		$route = new \Nano\Route\Subdomain('some', null);
		self::assertTrue($route->match('some-url'));

		$_SERVER['HTTP_HOST'] = 'some2.' . Nano::app()->config->get('web')->domain;
		self::assertFalse($route->match('some-url'));
	}

	public function testMatchSubdomainAndUrl() {
		$route = new \Nano\Route\Subdomain('some', 'some');

		$_SERVER['HTTP_HOST'] = Nano::app()->config->get('web')->domain;
		self::assertFalse($route->match('some'));

		$_SERVER['HTTP_HOST'] = 'some.' . Nano::app()->config->get('web')->domain;
		self::assertTrue($route->match('some'));
		self::assertFalse($route->match('some-url'));

		$_SERVER['HTTP_HOST'] = 'some2.' . Nano::app()->config->get('web')->domain;
		self::assertFalse($route->match('some'));
	}

	public function testParameters() {
		$route = new \Nano\Route\Subdomain('(?P<p1>some)', '(?P<p2>some)');

		$_SERVER['HTTP_HOST'] = 'some.' . Nano::app()->config->get('web')->domain;
		self::assertTrue($route->match('some'));
		self::assertArrayHasKey('p1', $route->matches());
		self::assertArrayHasKey('p2', $route->matches());
	}

}