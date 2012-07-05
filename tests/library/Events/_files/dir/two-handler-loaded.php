<?php

/** @var \Nano\Event\Manager $manager */
$manager
	->attach('test-event', array('Library_Events_Handler_C1', 'staticHandler'))
;