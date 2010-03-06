<?php

error_reporting(E_ALL);
ini_set('error_log', dirName(__FILE__) . '/reports/error.log');

define('TESTING', true);

require dirName(__FILE__) . '/../library/Nano.php';

Nano::instance();
Nano::config('selenium');
Nano_Db::setDefault('test');

