<?php

function library_events_handler_common(\Nano\Event $event) {
	$runs = $event->getArgument('runs', 0);
	++$runs;
	$event->setArgument('runs', $runs);

	if ($event->getArgument('text')) {
		$event->setArgument('text', '[' . $event->getArgument('text') . ']');
	}
}

function library_events_handler_f1(\Nano\Event $event) {
	library_events_handler_common($event);
	$event->setArgument('run-order', $event->getArgument('run-order') . '1');
}

class Library_Events_Handler_C1 {

	public static function staticHandler(\Nano\Event $event) {
		library_events_handler_common($event);
		$event->setArgument('run-order', $event->getArgument('run-order') . '3');
	}

	public function instanceHandler(\Nano\Event $event) {
		library_events_handler_common($event);
		$event->setArgument('run-order', $event->getArgument('run-order') . '2');
	}

}