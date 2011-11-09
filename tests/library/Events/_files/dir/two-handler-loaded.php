<?php

/** @var Event_Manager $manager */
$manager
	->attach('test-event', array('Library_Events_Handler_C1', 'staticHandler'))
;