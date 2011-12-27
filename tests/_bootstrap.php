<?php

error_reporting(E_ALL);
ini_set('error_log', __DIR__ . '/../build/error.log');
define('TESTING', true);

require_once __DIR__ . '/../application-example/bootstrap.php';

define('SELENIUM_ENABLE', Nano::config('selenium')->enabled);

if (SELENIUM_ENABLE) {
	PHPUnit_Extensions_SeleniumTestCase::$browsers = array((array)(Nano::config('selenium')->browser));
}

Nano_Db::setDefault('test');
Orm::setDefaultSource('test');