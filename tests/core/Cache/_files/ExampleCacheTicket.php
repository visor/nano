<?php

class ExampleCacheTicket extends Cache_Ticket {

	public function __construct(array $someDataToBuildKey) {
		parent::__construct($someDataToBuildKey['id'] . '-' . $someDataToBuildKey['key']);
		$this
			->tag($someDataToBuildKey['id'])
			->tag($someDataToBuildKey['key'])
		;
	}

}