<?php

class Test_Dispatcher implements Nano_Dispatcher_Custom {

	public function dispatch() {
		if (!isset($_COOKIE['accept'])) {
			return false;
		}

		return 'dispatched';
	}

}