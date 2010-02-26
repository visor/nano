<?php

define('SELENIUM_ENABLE', true);

require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

PHPUnit_Extensions_SeleniumTestCase::$browsers = array(
	array(
		  'name'    => 'Firefox'
		, 'browser' => '*firefox'
		, 'host'    => 'localhost'
		, 'port'    => 4444
		, 'timeout' => 30000
	)
);