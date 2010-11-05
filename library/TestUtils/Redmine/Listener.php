<?php

class TestUtils_Redmine_Listener extends PHPUnit_Extensions_TicketListener {

	/**
	 * @var PHPUnit_Framework_Test
	 */
	protected $test = null;

	/**
	 * @var PHPUnit_Framework_AssertionFailedError
	 */
	protected $failure = null;

	/**
	 * A failure occurred.
	 *
	 * @param  PHPUnit_Framework_Test                 $test
	 * @param  PHPUnit_Framework_AssertionFailedError $e
	 * @param  float                                  $time
	 */
	public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time) {
		$this->test    = $test;
		$this->failure = $e;
	}

	/**
	 * A test started.
	 *
	 * @param  PHPUnit_Framework_Test $test
	 */
	public function startTest(PHPUnit_Framework_Test $test) {
		parent::startTest($test);
		$this->test    = $test;
		$this->failure = null;
	}

	protected function updateTicket($ticketId, $newStatus, $message, $resolution) {
		TestUtils_Redmine_Issue::sendReport($ticketId, $newStatus, $message, $this->test, $this->failure);
	}

	protected function getTicketInfo($ticketId = null) {
		return array('status' => TestUtils_Redmine_Issue::convertStatus(TestUtils_Redmine_Issue::getIssue($ticketId)));
	}

}