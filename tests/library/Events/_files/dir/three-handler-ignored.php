<?php

/** @var \Nano\Event\Manager $manager */
$manager->attach('test-event', function (\Nano\Event $event) {
	throw new RuntimeException('was run');
});