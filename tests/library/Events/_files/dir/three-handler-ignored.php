<?php

/** @var Event_Manager $manager */
$manager->attach('test-event', function (Event $event) {
	throw new RuntimeException('was run');
});