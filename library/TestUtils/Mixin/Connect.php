<?php

namespace Nano\TestUtils\Mixin;

class Connect extends \Nano\TestUtils\Mixin {

	/**
	 * @return void
	 * @param string $host
	 * @param int    $port
	 * @param string $message
	 *
	 * @throws \PHPUnit_Framework_AssertionFailedError
	 */
	public function check($host, $port, $message) {
		try {
			$errNo = $errStr = null;
			if (!@fsockopen($host, $port, $errNo, $errStr, 1)) {
				throw new \PHPUnit_Framework_AssertionFailedError();
			}
		} catch (\PHPUnit_Framework_AssertionFailedError $e) {
			\PHPUnit_Framework_Assert::markTestSkipped(sprintf($message, $host, $port));
		}
	}

}