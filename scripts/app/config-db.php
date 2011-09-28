<?php return array(
	'default' => array(
		  'type'     => 'mysql'
		, 'dsn'      => 'host=localhost;dbname=db_name'
		, 'username' => 'db_user'
		, 'password' => 'db_pass'
		, 'options'  => array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
		)
		, 'log'      => APP_ROOT . DS . 'sql.log'
	)
	, 'test' => array(
		  'type'     => 'mysql'
		, 'dsn'      => 'host=localhost;dbname=db_name_test'
		, 'username' => 'db_name'
		, 'password' => 'db_pass'
		, 'options'  => array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
		)
		, 'log'      => APP_ROOT . DS . 'test-sql.log'
	)
);
