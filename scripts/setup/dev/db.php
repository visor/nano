<?php

$config = array(
	'default' => array(
		  'type'     => 'mysql'
		, 'dsn'      => 'host=localhost;dbname=avrora'
		, 'username' => 'user'
		, 'password' => ''
		, 'options'  => array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
		)
	)
	, 'test' => array(
		  'type'     => 'mysql'
		, 'dsn'      => 'host=localhost;dbname=avrora_test'
		, 'username' => 'user'
		, 'password' => ''
		, 'options'  => array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
		)
	)
);