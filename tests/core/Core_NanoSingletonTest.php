<?php

/**
 * @group core
 * @group framework
 */
class Core_NanoSingletonTest extends TestUtils_TestCase {

	public function testShouldThrowExceptionWhenCloning() {
		$this->setExpectedException('Nano_Exception', 'Unexpected call: __clone');
		clone Nano::instance();
	}

	public function testShouldThrowExceptionWhenTriingToSerialize() {
		$this->setExpectedException('Nano_Exception', 'Unexpected call: __sleep');
		serialize(Nano::instance());
	}

	public function testShouldThrowExceptionWhenTriingToUnSerialize() {
		$this->setExpectedException('Nano_Exception', 'Unexpected call: __wakeUp');
		unSerialize('O:4:"Nano":0:{}');
	}

}