<?php return array(
	'default' => array(
		  'type'     => 'mysql'
		, 'dsn'      => 'host=localhost;dbname=nano'
		, 'username' => 'user'
		, 'password' => ''
		, 'options'  => array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
		)
		, 'log'      => false
	)
	, 'test' => array(
		  'type'     => 'mysql'
		, 'dsn'      => 'host=localhost;dbname=nano_test'
		, 'username' => 'user'
		, 'password' => ''
		, 'options'  => array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
		)
		, 'log'      => APP_ROOT . DS . 'test-sql.log'
	)
);
