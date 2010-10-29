<?php

error_reporting(E_ALL);
ini_set('error_log', __DIR__ . '/reports/error.log');
define('TESTING', true);

require __DIR__ . '/../library/Nano.php';

Nano::instance();
Nano::config('selenium');
Nano_Db::setDefault('test');
$GLOBALS['PHPUNIT_COVERAGE_DATA_DIRECTORY'] = TESTS . DS . 'reports' . DS . 'coverage';