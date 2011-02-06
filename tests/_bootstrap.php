<?php

error_reporting(E_ALL);
ini_set('error_log', __DIR__ . '/reports/error.log');
define('TESTING', true);

if (!defined('APP')) {
	require __DIR__ . '/../library/Nano.php';
}

Nano::instance();
Nano_Db::setDefault('test');

define('SELENIUM_ENABLE', Nano::config('selenium')->enabled);

if (SELENIUM_ENABLE) {
	PHPUnit_Extensions_SeleniumTestCase::$browsers = array();
	$GLOBALS['PHPUNIT_COVERAGE_DATA_DIRECTORY'] = TESTS . DS . 'reports' . DS . 'coverage';
}