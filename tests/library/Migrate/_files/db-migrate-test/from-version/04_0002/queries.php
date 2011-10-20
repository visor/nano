<?php

$sql   = array();

$sql[] = 'insert into migration_test(id, comment) values (2000, ' . Nano::db()->quote('second migration') . ')';
