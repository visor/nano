<?php return array(
	'default' => array(
		  'type'     => 'mysql'
		, 'dsn'      => 'host=localhost;dbname=bonus'
		, 'username' => 'user'
		, 'password' => ''
		, 'options'  => array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
		)
		, 'log'      => $application->rootDir . '/sql.log'
	)
	, 'test' => array(
		  'type'     => 'mysql'
		, 'dsn'      => 'host=localhost;dbname=bonus_test'
		, 'username' => 'user'
		, 'password' => ''
		, 'options'  => array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
		)
		, 'log'      => $application->rootDir . '/test-sql.log'
	)
);