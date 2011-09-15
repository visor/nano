<?php

error_reporting(E_ALL);
ini_set('error_log', __DIR__ . '/../build/error.log');
define('TESTING', true);

require_once __DIR__ . '/../application/bootstrap.php';

Nano_Db::setDefault('test');