<?php

class TestUtils_TextUI_ResultPrinter extends PHPUnit_TextUI_ResultPrinter {

	const STATUS_OK         = 'OK';
	const STATUS_FAILURE    = 'FAILS';
	const STATUS_ERROR      = 'ERROR';
	const STATUS_SKIP       = 'SKIP';
	const STATUS_INCOMPLETE = 'INCOMPLETE';

	/**
	 * An error occurred.
	 *
	 * @param PHPUnit_Framework_Test $test
	 * @param Exception              $e
	 * @param float                  $time
	 */
	public function addError(PHPUnit_Framework_Test $test, Exception $e, $time) {
		$this->writeTestStatus(self::STATUS_ERROR);
		$this->lastTestFailed = true;
	}

	/**
	 * A failure occurred.
	 *
	 * @param PHPUnit_Framework_Test                 $test
	 * @param PHPUnit_Framework_AssertionFailedError $e
	 * @param float                                  $time
	 */
	public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time) {
		$this->writeTestStatus(self::STATUS_FAILURE);
		$this->lastTestFailed = true;
	}

	/**
	 * Incomplete test.
	 *
	 * @param PHPUnit_Framework_Test $test
	 * @param Exception              $e
	 * @param float                  $time
	 */
	public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time) {
		$this->writeTestStatus(self::STATUS_INCOMPLETE);
		$this->lastTestFailed = true;
	}

	/**
	 * Skipped test.
	 *
	 * @param PHPUnit_Framework_Test $test
	 * @param Exception              $e
	 * @param float                  $time
	 * @since Method available since Release 3.0.0
	 */
	public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time) {
		$this->writeTestStatus(self::STATUS_SKIP);
		$this->lastTestFailed = true;
	}

	/**
	 * A testsuite started.
	 *
	 * @param PHPUnit_Framework_TestSuite $suite
	 */
	public function startTestSuite(PHPUnit_Framework_TestSuite $suite) {
		if ($this->numTests == -1) {
			$this->numTests = count($suite);
		}
		$this->lastEvent = self::EVENT_TESTSUITE_START;
	}

	/**
	 * A testsuite ended.
	 *
	 * @param PHPUnit_Framework_TestSuite $suite
	 */
	public function endTestSuite(PHPUnit_Framework_TestSuite $suite) {
		$this->lastEvent = self::EVENT_TESTSUITE_END;
	}

	/**
	 * A test started.
	 *
	 * @param PHPUnit_Framework_Test $test
	 */
	public function startTest(PHPUnit_Framework_Test $test) {
		$this->numTestsRun++;
		$this->lastEvent = self::EVENT_TEST_START;
		$len  = strLen($this->numTests);
		$name = str_pad(PHPUnit_Util_Test::describe($test, true), 100, ' ', STR_PAD_RIGHT);
		$this->write(sprintf('[%' . $len . 'd / %d ] %s', $this->numTestsRun, $this->numTests, $name));
	}

	/**
	 * A test ended.
	 *
	 * @param PHPUnit_Framework_Test $test
	 * @param float                  $time
	 */
	public function endTest(PHPUnit_Framework_Test $test, $time) {
		if (!$this->lastTestFailed) {
			$this->writeTestStatus(self::STATUS_OK);
		}

		if ($test instanceof PHPUnit_Framework_TestCase) {
			/** @var PHPUnit_Framework_TestCase $test */
			$this->numAssertions += $test->getNumAssertions();
		}

		$this->lastEvent      = self::EVENT_TEST_END;
		$this->lastTestFailed = false;
	}

	protected function writeTestStatus($status) {
		$this->write('[ ' . $status . ' ]' . PHP_EOL);
	}

}