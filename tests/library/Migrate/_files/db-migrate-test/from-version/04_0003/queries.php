<?php

$sql   = array();

$sql[] = 'insert into migration_test(id, comment) values (300, ' . Nano::db()->quote('3rd migration') . ')';
