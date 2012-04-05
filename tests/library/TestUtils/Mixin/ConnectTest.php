<?php

class TestUtils_Mixin_ConnectTest extends TestUtils_TestCase {

	public function testCheckShouldMarkTestSkippedWhenFails() {
		try {
			$mixin = new TestUtils_Mixin_Connect();
			$mixin->check('0.0.0.1', '80', 'Test connection to %s:%d');
			self::fail('Exception no throws');
		} catch (PHPUnit_Framework_SkippedTestError $e) {
			self::assertEquals('Test connection to 0.0.0.1:80', $e->getMessage());
		}
	}

	public function testCheckShouldDoNothingWhenConnectionValid() {
		try {
			$mixin = new TestUtils_Mixin_Connect();
			$mixin->check('google.com', '80', 'Test connection to google');
		} catch (PHPUnit_Framework_SkippedTestError $e) {
			self::fail('Should not throws');
		}
	}

}