<?php

class TestRunnableRoute extends Nano_Route implements Nano_Route_Runnable {

	const LOCATION = 'test';

	/**
	 * @var boolean
	 */
	protected $wasRun;

	public function __construct() {
		$this->wasRun = false;
	}

	/**
	 * @return boolean
	 * @param string $location
	 */
	public function match($location) {
		return self::LOCATION === mb_strToLower($location, 'UTF-8');
	}

	public function run() {
		$this->wasRun = true;
	}

	/**
	 * @return boolean
	 */
	public function wasRun() {
		return $this->wasRun;
	}

}