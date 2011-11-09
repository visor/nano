<?php

/** @var Event_Manager $manager */
$manager
	->attach('test-event', 'library_events_handler_f1')
	->attach('another-test-event', array('Library_Events_Handler_C1', 'staticHandler'))
;