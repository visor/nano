<?php

class Library_Events_TestHandler implements Event_Handler {

	public $someEventRised = 0;

	public function onSomeEvent(Event $event) {
		++$this->someEventRised;
	}

}