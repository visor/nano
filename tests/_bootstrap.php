<?php

error_reporting(E_ALL);
ini_set('error_log', __DIR__ . '/../build/error.log');
define('TESTING', true);

require_once __DIR__ . '/../library/Nano/Loader.php';
$loader = new Nano_Loader();
$loader->register();

//if (!defined('APP')) {
//	require __DIR__ . '/../library/Nano.php';
//}
//
//require APP . DS. 'bootstrap.php';
//
//Nano_Db::setDefault('test');
//
//define('SELENIUM_ENABLE', Nano::config('selenium')->enabled);
//
//if (SELENIUM_ENABLE) {
//	PHPUnit_Extensions_SeleniumTestCase::$browsers = array((array)(Nano::config('selenium')->browser));
//	$GLOBALS['PHPUNIT_COVERAGE_DATA_DIRECTORY'] = TESTS . DS . 'reports' . DS . 'coverage';
//}
//
//Nano::config()->set('cdn', (object)array('servers' => array()));