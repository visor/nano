<?php

error_reporting(E_ALL);
ini_set('error_log', __DIR__ . '/../build/error.log');
define('TESTING', true);

require_once __DIR__ . '/../application-example/bootstrap.php';

/** @var Application $application */

define('SELENIUM_ENABLE', $application->config->get('selenium')->enabled);

if (SELENIUM_ENABLE) {
	PHPUnit_Extensions_SeleniumTestCase::$browsers = array((array)($application->config->get('selenium')->browser));
}

$GLOBALS['application'] = $application;
Nano_Db::setDefault('test');
Orm::setDefaultSource('test');