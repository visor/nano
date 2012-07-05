<?php

class Library_Events_TestHandler implements \Nano\Event\Handler {

	public $someEventRised = 0;

	public function onSomeEvent(\Nano\Event $event) {
		++$this->someEventRised;
	}

}