<?php

/**
 * @group framework
 * @group helpers
 */
class Core_HelperBrokerTest extends TestUtils_TestCase {

	protected function setUp() {
		Nano::modules()->append('example');
	}

	public function testSearchingModuleClasses() {
		$helper = Nano::helper()->get('example');

		self::assertInstanceOf('M_Example_Helper_Example', $helper);
	}

	protected function tearDown() {
		Nano::modules()->remove('example');
	}

}