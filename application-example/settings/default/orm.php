<?php return array(
	'default' => array(
		'dsn'          => 'mysql://host=localhost;dbname=nano'
		, 'default'    => true
		, 'datasource' => 'Orm_DataSource_Pdo_Mysql'
		, 'username'   => 'user'
		, 'password'   => ''
		, 'log'        => false
	)
	, 'test' => array(
		'dsn'          => 'mysql://host=localhost;dbname=nano_test'
		, 'datasource' => 'Orm_DataSource_Pdo_Mysql'
		, 'username'   => 'user'
		, 'password'   => ''
		, 'log'        => APP_ROOT . DS . 'test-sql.log'
	)
);
