<?php

$sql   = array();

$sql[] = 'insert into migration_test(id, comment) values (500, ' . Nano::db()->quote('5th migration') . ')';
