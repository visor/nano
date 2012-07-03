<?php

class Test_Dispatcher implements \Nano\Dispatcher\Custom {

	public function dispatch() {
		if (!isset($_COOKIE['accept'])) {
			return false;
		}

		return 'dispatched';
	}

}