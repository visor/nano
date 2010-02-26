<?php

$sql   = array();

$sql[] = 'insert into migration_test(id, comment) values (200, ' . Nano::db()->quote('second migration') . '';
