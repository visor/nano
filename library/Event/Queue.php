<?php

class Event_Queue extends SplPriorityQueue {

	protected $serial = PHP_INT_MAX;

	public function insert($value, $priority) {
		parent::insert($value, array($priority, $this->serial--));
	}

}