<?php

$sql   = array();

$sql[] =
	'create table migration_test('
		. 'id integer primary key'
		. ', comment text'
	. ')'
;

$sql[] = 'insert into migration_test(id, comment) values (100, ' . Nano::db()->quote('first migration') . ')';
