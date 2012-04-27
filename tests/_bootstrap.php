<?php

error_reporting(E_ALL);
ini_set('error_log', __DIR__ . '/../build/error.log');
define('TESTING', true);

/** @var Application $application */
require_once __DIR__ . '/../application-example/bootstrap.php';

if ($application->config->exists('selenium')) {
	define('SELENIUM_ENABLE', $application->config->get('selenium')->enabled);
	if (SELENIUM_ENABLE) {
		PHPUnit_Extensions_SeleniumTestCase::$browsers = array((array)($application->config->get('selenium')->browser));
	}
}

$GLOBALS['application'] = $application;