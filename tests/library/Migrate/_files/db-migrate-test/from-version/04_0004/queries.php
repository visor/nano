<?php

$sql   = array();

$sql[] = 'insert into migration_test(id, comment) values (400, ' . Nano::db()->quote('4th migration') . ')';
