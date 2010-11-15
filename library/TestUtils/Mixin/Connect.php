<?php

class TestUtils_Mixin_Connect extends TestUtils_Mixin {

	/**
	 * @return void
	 * @param string $host
	 * @param int $port
	 * @param string $message
	 */
	public function check($host, $port, $message) {
		try {
			$errNo = $errStr = null;
			if (!@fsockopen($host, $port, $errNo, $errStr, 1)) {
				PHPUnit_Framework_Assert::fail();
			}
		} catch (Exception $e) {
			PHPUnit_Framework_Assert::markTestSkipped(sprintf($message, $host, $port));
		}
	}

}