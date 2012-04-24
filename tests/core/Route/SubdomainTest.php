<?php

/**
 * @group core
 * @group routes
 */
class Core_Route_SubdomainTest extends TestUtils_TestCase {

	public function testMatchSubdomainOnly() {
		$route = new Nano_Route_Subdomain('.+', null);

		$_SERVER['HTTP_HOST'] = $GLOBALS['application']->config->get('web')->domain;
		self::assertFalse($route->match('some-url'));

		$_SERVER['HTTP_HOST'] = 'some.' . $GLOBALS['application']->config->get('web')->domain;
		self::assertTrue($route->match('some-url'));

		$route = new Nano_Route_Subdomain('some', null);

		self::assertTrue($route->match('some-url'));

		$_SERVER['HTTP_HOST'] = 'some2.' . $GLOBALS['application']->config->get('web')->domain;
		self::assertFalse($route->match('some-url'));
	}

	public function testMatchSubdomainAndUrl() {
		$route = new Nano_Route_Subdomain('some', 'some');

		$_SERVER['HTTP_HOST'] = $GLOBALS['application']->config->get('web')->domain;
		self::assertFalse($route->match('some'));

		$_SERVER['HTTP_HOST'] = 'some.' . $GLOBALS['application']->config->get('web')->domain;
		self::assertTrue($route->match('some'));
		self::assertFalse($route->match('some-url'));

		$_SERVER['HTTP_HOST'] = 'some2.' . $GLOBALS['application']->config->get('web')->domain;
		self::assertFalse($route->match('some'));
	}

	public function testParameters() {
		$route = new Nano_Route_Subdomain('(?P<p1>some)', '(?P<p2>some)');

		$_SERVER['HTTP_HOST'] = 'some.' . $GLOBALS['application']->config->get('web')->domain;
		self::assertTrue($route->match('some'));
		self::assertArrayHasKey('p1', $route->matches());
		self::assertArrayHasKey('p2', $route->matches());
	}

}